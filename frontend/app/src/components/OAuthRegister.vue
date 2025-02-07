<template>
    <div>
      <h2>註冊憑證</h2>

      <div class="option-buttons">
        <button @click="registerApiKey" class="action-button">建立 API 金鑰</button>
        <button @click="toggleOAuthForm" class="action-button">建立 OAuth 2.0 用戶端 ID</button>
      </div>

      <!-- ✅ OAuth 2.0 用戶端 ID 設定表單 -->
      <div v-if="showOAuthForm" class="oauth-form">
        <h3 class="section-title">OAuth 2.0 用戶端設定</h3>

        <div class="input-group">
          <label class="input-label">名稱:</label>
          <input v-model="oauthData.name" type="text" class="input-field" placeholder="輸入應用程式名稱">
        </div>

        <div class="input-group">
          <label class="input-label">已授權的 JavaScript 來源:</label>
          <input v-model="oauthData.javascript_origin" type="text" class="input-field" placeholder="例: http://localhost">
        </div>

        <div class="input-group">
          <label class="input-label">已授權的重新導向 URI:</label>
          <input v-model="oauthData.redirect_uri" type="text" class="input-field" placeholder="例: http://localhost/auth/callback">
        </div>

        <button @click="registerOAuthClient" class="submit-button">建立 OAuth 2.0 用戶端 ID</button>
      </div>

      <!-- ✅ API 回應結果 -->
      <div v-if="response" class="response-section">
        <h3 class="section-title">回應結果</h3>
        <pre class="response-content">{{ response }}</pre>
      </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      response: null,
      showOAuthForm: false, // 控制 OAuth 設定表單的顯示與隱藏
      oauthData: {
        name: '',
        javascript_origin: '',
        redirect_uri: ''
      }
    };
  },

  mounted() {
    // ✅ 檢查用戶是否登入（確保 localStorage 內有 access_token）
    const token = localStorage.getItem('access_token');
    console.log("進入 /register 頁面，Token:", token);

    if (!token) {
      alert("請先登入！");
      this.$router.push('/admin/login'); // 若未登入則跳轉至登入頁
    }
  },

  methods: {
    toggleOAuthForm() {
      this.showOAuthForm = !this.showOAuthForm;
    },

    async registerApiKey() {
      try {
        const token = localStorage.getItem('access_token');
        if (!token) {
          alert('請先登入！');
          this.$router.push('/admin/login');
          return;
        }

        const res = await axios.post('http://localhost/api/register-api-key', {}, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });

        this.response = res.data;
        alert('成功建立 API 金鑰');
      } catch (error) {
        this.response = error.response ? error.response.data : error.message;
      }
    },

    async registerOAuthClient() {
      try {
        if (!this.oauthData.name || !this.oauthData.javascript_origin || !this.oauthData.redirect_uri) {
          alert('請填寫所有欄位');
          return;
        }

        const token = localStorage.getItem('access_token');
        if (!token) {
          alert('請先登入！');
          this.$router.push('/admin/login');
          return;
        }

        const res = await axios.post('http://localhost/api/oauth/clients', {
          name: this.oauthData.name,
          javascript_origin: this.oauthData.javascript_origin,
          redirect_uri: this.oauthData.redirect_uri
        }, {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });

        this.response = res.data;
        alert('成功建立 OAuth 2.0 用戶端 ID');
      } catch (error) {
        this.response = error.response ? error.response.data : error.message;
      }
    }
  }
};
</script>

<style scoped>
h2 {
  font-size: 24px;
  margin-bottom: 20px;
}

.option-buttons {
  margin-bottom: 20px;
}

.action-button {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 10px 20px;
  margin-right: 10px;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.action-button:hover {
  background-color: #45a049;
}

.oauth-form {
  margin-top: 20px;
  padding: 15px;
  border: 1px solid #ddd;
  border-radius: 6px;
  background-color: #f9f9f9;
}

.input-group {
  margin-bottom: 15px;
}

.input-label {
  display: block;
  font-weight: bold;
  margin-bottom: 5px;
  color: #555;
}

.input-field {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.submit-button {
  background-color: #007BFF;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.submit-button:hover {
  background-color: #0056b3;
}

.response-section {
  background-color: #fff;
  padding: 15px;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-top: 20px;
}

.response-content {
  white-space: pre-wrap;
  word-wrap: break-word;
  font-family: monospace;
  color: #333;
}
</style>
