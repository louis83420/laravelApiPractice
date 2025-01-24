<template>
    <div>
      <!-- Header -->
      <header class="api-test-top">
        <nav class="nav-bar">
          <RouterLink to="/login" class="nav-link">登入</RouterLink>
        </nav>
      </header>
  
      <!-- API Tester Container -->
      <div class="api-tester">
        <h2 class="title">API 測試用</h2>
  
        <!-- API URL 輸入框 -->
        <div class="input-group">
          <label for="url" class="input-label">API URL:</label>
          <input v-model="url" type="text" class="url-input" placeholder="輸入API的URL" />
        </div>
  
        <!-- 方法選擇 -->
        <div class="input-group">
          <label for="method" class="input-label">方法:</label>
          <select v-model="method" class="method-select">
            <option value="GET">GET</option>
            <option value="POST">POST</option>
            <option value="PUT">PUT</option>
            <option value="DELETE">DELETE</option>
          </select>
        </div>
  
        <!-- 資料表名稱輸入框與取得欄位按鈕 -->
        <div class="input-group">
          <label for="table" class="input-label">資料表名稱:</label>
          <input v-model="tableName" type="text" class="table-input" placeholder="輸入資料表名稱" />
          <button @click="fetchFields" class="action-button">取得欄位</button>
        </div>
  
        <!-- 顯示欄位資訊 -->
        <div v-if="fields.length" class="fields-section">
          <h3 class="section-title">參數</h3>
          <div v-for="(field, index) in fields" :key="index" class="field-input-group">
            <label class="field-label">{{ field }}</label>
            <input v-model="params[field]" class="field-input" placeholder="輸入參數的值" />
          </div>
        </div>
  
        <!-- 新增三方 API 測試欄位 -->
        <div class="toggle-section">
          <button @click="toggleThirdPartyFields" class="toggle-button">
            {{ showThirdPartyFields ? '隱藏三方 API 請求參數' : '顯示三方 API 請求參數' }}
          </button>
        </div>
  
        <!-- 三方 API 請求參數 -->
        <div v-if="showThirdPartyFields" class="third-party-section">
          <h3 class="section-title">三方 API 請求參數</h3>
          <div class="input-group">
            <label for="grant_type" class="input-label">grant_type:</label>
            <input v-model="thirdPartyParams.grant_type" type="text" class="third-party-input" placeholder="輸入 grant_type" />
          </div>
          <div class="input-group">
            <label for="client_id" class="input-label">client_id:</label>
            <input v-model="thirdPartyParams.client_id" type="text" class="third-party-input" placeholder="輸入 client_id" />
          </div>
          <div class="input-group">
            <label for="client_secret" class="input-label">client_secret:</label>
            <input v-model="thirdPartyParams.client_secret" type="password" class="third-party-input" placeholder="輸入 client_secret" />
          </div>
          <div class="input-group">
            <label for="scope" class="input-label">scope:</label>
            <input v-model="thirdPartyParams.scope" type="text" class="third-party-input" placeholder="輸入 scope" />
          </div>
          <div v-if="thirdPartyParams.grant_type === 'password'" class="input-group">
            <label for="email" class="input-label">email:</label>
            <input v-model="thirdPartyParams.email" type="text" class="third-party-input" placeholder="輸入 email" />
            <label for="password" class="input-label">password:</label>
            <input v-model="thirdPartyParams.password" type="password" class="third-party-input" placeholder="輸入 password" />
          </div>
          <button @click="fetchAccessToken" class="action-button">取得 Access Token</button>
        </div>
  
        <!-- Token 輸入框 -->
        <div class="input-group">
          <label for="token" class="input-label">Token</label>
          <input id="token" type="text" v-model="token" class="token-input" placeholder="請輸入您的 Token" />
        </div>
  
        <!-- 發送按鈕 -->
        <button :disabled="loading" @click="sendRequest" class="send-button">發送</button>
  
        <!-- 回應結果 -->
        <div v-if="response" class="response-section">
          <h3 class="section-title">回應結果</h3>
          <pre class="response-content">{{ response }}</pre>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        url: '',
        method: 'GET',
        tableName: '',
        fields: [],
        params: {},
        response: null,
        showThirdPartyFields: false,
        thirdPartyParams: {
          grant_type: '',
          client_id: '',
          client_secret: '',
          scope: '',
        },
        token: '',
        loading: false,
      };
    },
    methods: {
      toggleThirdPartyFields() {
        this.showThirdPartyFields = !this.showThirdPartyFields;
      },
      async fetchFields() {
        try {
          const res = await axios.get(`http://localhost/api/get-fields?table=${this.tableName}`);
          if (res.data && Array.isArray(res.data.field)) {
            this.fields = res.data.field;
            this.params = {};
            this.fields.forEach((field) => {
              this.params[field] = '';
            });
            alert(`成功取得資料表 "${this.tableName}" 的欄位資訊`);
          } else {
            throw new Error('回傳的資料格式不正確');
          }
        } catch (error) {
          console.error('取得欄位資訊失敗:', error);
          alert('無法取得欄位資訊，請檢查 API 路徑或資料表名稱');
        }
      },
      async sendRequest() {
        this.loading = true;
        try {
          const headers = {};
          if (this.token) {
            headers['Authorization'] = `Bearer ${this.token}`;
          }
          const parsedParams = this.params ? JSON.parse(JSON.stringify(this.params)) : {};
          const res = await axios({
            method: this.method,
            url: this.url,
            data: parsedParams,
            headers,
          });
          this.response = res.data;
        } catch (error) {
          this.response = error.response ? error.response.data : error.message;
        } finally {
          this.loading = false;
        }
      },
      async fetchAccessToken() {
        try {
          const data = {
            grant_type: this.thirdPartyParams.grant_type,
            client_id: this.thirdPartyParams.client_id,
            client_secret: this.thirdPartyParams.client_secret,
            scope: this.thirdPartyParams.scope,
          };
  
          if (this.thirdPartyParams.grant_type === 'password') {
            data.email = this.thirdPartyParams.email;
            data.password = this.thirdPartyParams.password;
          }
  
          const res = await axios.post('http://localhost/api/oauth/token', data);
          this.response = res.data.access_token;
          alert('Access Token 獲取成功');
        } catch (error) {
          console.error('取得 Access Token 失敗:', error);
          alert('取得 Access Token 失敗，請檢查參數是否正確');
        }
      },
    },
  };
  </script>
  
  <style scoped>
  /* 全域設定 */
  body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
  }
  
  /* Header */
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
    background-color: rgba(255, 255, 255, 0.1);
  }
  
  /* API Tester Container */
  .api-tester {
    padding: 80px 20px 20px;
    max-width: 800px;
    margin: 0 auto;
  }
  
  .title {
    font-size: 24px;
    color: #333;
    margin-bottom: 20px;
  }
  
  /* 輸入框群組 */
  .input-group {
    margin-bottom: 15px;
  }
  
  .input-label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    color: #555;
  }
  
  .url-input,
  .method-select,
  .table-input,
  .third-party-input,
  .token-input,
  .field-input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
  }
  
  /* 按鈕 */
  .action-button,
  .toggle-button,
  .send-button {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s;
  }
  
  .action-button:hover,
  .toggle-button:hover,
  .send-button:hover {
    background-color: #45a049;
  }
  
  .send-button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
  }
  
  /* 欄位輸入區 */
  .fields-section,
  .third-party-section {
    margin-top: 20px;
  }
  
  .section-title {
    font-size: 18px;
    color: #333;
    margin-bottom: 10px;
  }
  
  .field-input-group {
    margin-bottom: 10px;
  }
  
  .field-label {
    font-weight: bold;
    color: #555;
  }
  
  /* 回應結果 */
  .response-section {
    margin-top: 20px;
    background-color: #fff;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
  }
  
  .response-content {
    white-space: pre-wrap;
    word-wrap: break-word;
    font-family: monospace;
    color: #333;
  }
  </style>