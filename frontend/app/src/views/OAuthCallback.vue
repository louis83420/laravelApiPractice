<!-- views/OAuthCallback.vue -->
<template>
    <div>
    <h2>OAuth 授權回調</h2>
    </div>
    </template>
    
    <script setup>
    import axios from 'axios';
    import { ref, onMounted } from 'vue';
    import { useRouter } from 'vue-router';
    
    const code = ref('');
    const state = ref('');
    const error = ref(null);
    const router = useRouter();
    
    onMounted(async () => {
    try {
    code.value = new URLSearchParams(window.location.search).get('code');
    state.value = new URLSearchParams(window.location.search).get('state');
    
    if (!code.value || !state.value) throw new Error('缺少必要參數');
    
    // 與服務器交換令牌（假設有後端 API）
    await axios.post('http://localhost/oauth/token', {
    grant_type:'authorization_code',
    code : code.value,
    redirect_uri : encodeURIComponent('http://localhost/auth/callback'),
    client_id : "5",
    client_secret : "4b1q7m34F8ZCbLpJjz4qCvMZIjPf5pUi25H0w8Ri", // 你需要填入自己的 client secret
    }, 
    {
    headers:{
    'Content-Type': 'application/x-www-form-urlencoded'
    }
    })
    .then((res) => {
    localStorage.setItem("token", res.data.access_token);
    router.push('/');
    })
    .catch((err) => console.error(err));
    
    } catch (error) {
    error.value = error.message;
    }
    });
    </script>
    
    <style scoped>
    
    </style>
    