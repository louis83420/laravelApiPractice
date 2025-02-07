<template>
    <div>
      <h2>OAuth 2.0 登入中...</h2>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    async created() {
      const urlParams = new URLSearchParams(window.location.search);
      const authCode = urlParams.get('code');
  
      if (authCode) {
        try {
          const res = await axios.post('http://localhost/oauth/token', {
            grant_type: 'authorization_code',
            client_id: '5', // 你的 Client ID
            client_secret: '4b1q7m34F8ZCbLpJjz4qCvMZIjPf5pUi25H0w8Ri',
            redirect_uri: 'http://localhost/auth/callback',
            code: authCode,
          });
  
          // 儲存 Token
          localStorage.setItem('access_token', res.data.access_token);
          alert('登入成功！');
          this.$router.push('/'); // 跳轉回首頁
        } catch (error) {
          console.error('OAuth 交換 Access Token 失敗', error);
          alert('OAuth 登入失敗');
        }
      } else {
        alert('沒有取得授權碼');
      }
    }
  };
  </script>
  