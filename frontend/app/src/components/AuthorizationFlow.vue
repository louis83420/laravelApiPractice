<!-- src/components/OAuth/AuthorizationFlow.vue -->
<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

// 狀態管理
const authStatus = ref('init')
const errorMessage = ref('')
const clientInfo = ref(null)

// 核心授權參數
const authParams = ref({
  client_id: '',
  redirect_uri: '',
  response_type: 'code',
  scope: [],
  state: '',
  code_challenge: '',
  code_challenge_method: 'S256'
})

// PKCE 相關函數
const generateCodeVerifier = () => {
  const array = new Uint8Array(32)
  window.crypto.getRandomValues(array)
  return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('')
}

const generateCodeChallenge = async (verifier) => {
  const encoder = new TextEncoder()
  const data = encoder.encode(verifier)
  const digest = await window.crypto.subtle.digest('SHA-256', data)
  return btoa(String.fromCharCode(...new Uint8Array(digest)))
    .replace(/\+/g, '-')
    .replace(/\//g, '_')
    .replace(/=+$/, '')
}

// 初始化授權流程
const initAuthorization = async () => {
  try {
    authStatus.value = 'loading'

    // 從伺服器獲取客戶端資訊
    const clientResponse = await axios.get(`/api/oauth/clients/${authParams.value.client_id}`)
    clientInfo.value = clientResponse.data

    // 驗證 redirect_uri
    if (!clientInfo.value.redirect_uris.includes(authParams.value.redirect_uri)) {
      throw new Error('未授權的重定向 URI')
    }

    // 生成 PKCE 參數
    const codeVerifier = generateCodeVerifier()
    sessionStorage.setItem('pkce_verifier', codeVerifier)
    authParams.value.code_challenge = await generateCodeChallenge(codeVerifier)

    // 跳轉到授權端點
    window.location.href = `/oauth/authorize?${new URLSearchParams(authParams.value)}`
    
  } catch (error) {
    handleAuthorizationError(error)
  }
}

// 處理回調
const handleCallback = async () => {
  try {
    const { code, state, error } = route.query

    // 驗證 state
    if (state !== authParams.value.state) {
      throw new Error('State 參數不匹配')
    }

    if (error) {
      throw new Error(`授權伺服器錯誤: ${error}`)
    }

    // 交換令牌
    const tokenResponse = await axios.post('/oauth/token', {
      grant_type: 'authorization_code',
      client_id: authParams.value.client_id,
      redirect_uri: authParams.value.redirect_uri,
      code,
      code_verifier: sessionStorage.getItem('pkce_verifier')
    })

    // 儲存令牌
    localStorage.setItem('access_token', tokenResponse.data.access_token)
    localStorage.setItem('refresh_token', tokenResponse.data.refresh_token)
    
    authStatus.value = 'authorized'
    sessionStorage.removeItem('pkce_verifier')

  } catch (error) {
    handleAuthorizationError(error)
  }
}

// 錯誤處理
const handleAuthorizationError = (error) => {
  console.error('OAuth Error:', error)
  errorMessage.value = error.response?.data?.error || error.message
  authStatus.value = 'error'
  
  // 自動清除錯誤狀態
  setTimeout(() => {
    errorMessage.value = ''
    authStatus.value = 'init'
  }, 5000)
}

// 生命週期鉤子
onMounted(() => {
  if (route.path === '/oauth/callback') {
    handleCallback()
  } else {
    // 初始化參數
    authParams.value = {
      ...authParams.value,
      client_id: route.query.client_id,
      redirect_uri: route.query.redirect_uri || window.location.origin + '/callback',
      scope: (route.query.scope || '').split('+'),
      state: route.query.state || crypto.randomUUID()
    }
  }
})
</script>

<template>
  <div class="oauth-flow">
    <div v-if="authStatus === 'init'" class="authorization-prompt">
      <h3>{{ clientInfo?.name }} 請求以下權限：</h3>
      <ul class="scope-list">
        <li v-for="scope in clientInfo?.scopes" :key="scope">
          {{ scopeDescription(scope) }}
        </li>
      </ul>
      <button @click="initAuthorization" class="authorize-button">
        授權
      </button>
      <button @click="router.push('/')" class="cancel-button">
        取消
      </button>
    </div>

    <div v-if="authStatus === 'loading'" class="loading">
      <div class="spinner"></div>
      <p>正在連接授權伺服器...</p>
    </div>

    <div v-if="authStatus === 'authorized'" class="success">
      <svg><!-- 成功圖標 --></svg>
      <p>授權成功！正在跳轉...</p>
    </div>

    <div v-if="authStatus === 'error'" class="error">
      <svg><!-- 錯誤圖標 --></svg>
      <p>{{ errorMessage }}</p>
      <button @click="authStatus = 'init'">重試</button>
    </div>
  </div>
</template>

<style scoped>
.oauth-flow {
  max-width: 600px;
  margin: 2rem auto;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.scope-list {
  list-style: none;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 4px;
  margin: 1rem 0;
}

.authorize-button, .cancel-button {
  padding: 0.75rem 1.5rem;
  margin: 0 0.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.authorize-button {
  background: #4CAF50;
  color: white;
}

.cancel-button {
  background: #f44336;
  color: white;
}

.loading .spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
