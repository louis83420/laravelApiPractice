<template>
    <div>
      <h1>管理員後台</h1>
      <p>歡迎來到管理員後台。</p>
  
      <!-- 登出按鈕 -->
      <button @click="handleLogout">登出</button>
    </div>
  </template>
  
  <script>
  export default {
    methods: {
      handleLogout() {
        localStorage.removeItem('access_token'); // 清除 Token
        this.$router.push('/adminlogin'); // 導回登入頁面
      },
    },
  };
  </script>
  
  <style scoped>
  h1 {
    color: #333;
  }
  button {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
  }
  button:hover {
    background-color: #0056b3;
  }
  </style>
  