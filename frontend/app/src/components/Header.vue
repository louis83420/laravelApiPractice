<template>
    <header class="api-test-top">         
      <nav class="nav-bar">
        <RouterLink to="/login" class="nav-link">登入</RouterLink> 
        <button @click="exportUsers" class="nav-link">匯出</button>
        <button @click="openFileInput" class="nav-link">匯入</button>
        <input type="file" ref="fileInput" @change="importUsers" style="display: none;" />
      </nav>
    </header>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    methods: {
      async exportUsers() {
        try {
          window.location.href = 'http://localhost/api/export-users';
        } catch (error) {
          console.error('匯出失敗:', error);
          alert('匯出失敗，請檢查 API 是否正常運作');
        }
      },
  
      openFileInput() {
        this.$refs.fileInput.click();
      },
  
      async importUsers(event) {
        const file = event.target.files[0];
        if (!file) {
          alert('請選擇一個 Excel 檔案');
          return;
        }
  
        const formData = new FormData();
        formData.append('file', file);
  
        try {
          await axios.post('http://localhost/api/import-users', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          });
  
          alert('使用者資料匯入成功！');
        } catch (error) {
          console.error('匯入失敗:', error);
          alert('匯入失敗，請檢查 API 是否正常運作');
        }
      }
    }
  };
  </script>
  
  <style scoped>
  /* header */
  .api-test-top {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background-color: #4CAF50;
    padding: 10px 20px;
    z-index: 1000;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  
  .nav-bar {
    display: flex;
    justify-content: flex-end;
  }
  
  .nav-link {
    color: white;
    text-decoration: none;
    font-weight: bold;
    padding: 5px 10px;
    border-radius: 4px;
    transition: background-color 0.3s;
  }
  
  .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.2);
  }

  button {
  all: unset; /* 清除所有瀏覽器預設樣式 */
  cursor: pointer; /* 讓按鈕仍保持點擊效果 */
  }
  </style>
  