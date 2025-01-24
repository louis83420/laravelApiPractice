<template>
  <div>
    <header class="api-test-top">      
      <nav>
        <RouterLink to="/login">登入</RouterLink>        
      </nav>
    </header>
  </div>
  <div class="api-tester">
    <h2>API 測試用</h2>

    <!-- API URL 輸入框 -->
    <div>
      <label for="url">API URL:</label>
      <input v-model="url" type="text" class="url-input" placeholder="輸入API的URL" />
    </div>

    <!-- 方法選擇 -->
    <div>
      <label for="method">方法:</label>
      <select v-model="method">
        <option value="GET">GET</option>
        <option value="POST">POST</option>
        <option value="PUT">PUT</option>
        <option value="DELETE">DELETE</option>
      </select>
    </div>

    <!-- 資料表名稱輸入框與取得欄位按鈕 -->
    <div>
      <label for="table">資料表名稱:</label>
      <input v-model="tableName" type="text" placeholder="輸入資料表名稱" />
      <button @click="fetchFields">取得欄位</button>
    </div>

    <!-- 顯示欄位資訊 -->
    <div v-if="fields.length">
      <h3>參數</h3>
      <div v-for="(field, index) in fields" :key="index">
        <label>{{ field }}</label>
        <input v-model="params[field]" placeholder="輸入參數的值" />
        <!-- <input
          v-model="params[field]"
          :placeholder="requiredFields.includes(field) ? '必填' : ''"
        /> -->
      </div>
    </div>
    <!-- 新增三方 API 測試欄位 -->
    <div>
      <button @click="toggleThirdPartyFields">
        {{ showThirdPartyFields ? '隱藏三方 API 請求參數' : '顯示三方 API 請求參數' }}
      </button>
    </div>
    <div v-if="showThirdPartyFields">
      <h3>三方 API 請求參數</h3>
      <div>
        <label for="grant_type">grant_type:</label>
        <input v-model="thirdPartyParams.grant_type" type="text" placeholder="輸入 grant_type" />
      </div>
      <div>
        <label for="client_id">client_id:</label>
        <input v-model="thirdPartyParams.client_id" type="text" placeholder="輸入 client_id" />
      </div>
      <div>
        <label for="client_secret">client_secret:</label>
        <input v-model="thirdPartyParams.client_secret" type="password" placeholder="輸入 client_secret" />
      </div>
      <div>
        <label for="scope">scope:</label>
        <input v-model="thirdPartyParams.scope" type="text" placeholder="輸入 scope" />
      </div>
      <div v-if="thirdPartyParams.grant_type === 'password'">
        <label for="email">email:</label>
        <input v-model="thirdPartyParams.email" type="text" placeholder="輸入 email" />
        <label for="password">password:</label>
        <input v-model="thirdPartyParams.password" type="password" placeholder="輸入 password" />
      </div>
      <button @click="fetchAccessToken">取得 Access Token</button>
    </div>
    <div>
      <label for="token">Token</label>
      <input id="token" type="text" v-model="token" placeholder="請輸入您的 Token">
    </div>
    <!-- 發送按鈕 -->
    <button :disabled="loading" @click="sendRequest">發送</button>


    <!-- 回應結果 -->
    <div v-if="response">
      <h3>回應結果</h3>
      <pre>{{ response }}</pre>
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
    };
  },
  methods: {
      toggleThirdPartyFields() {        
        this.showThirdPartyFields = !this.showThirdPartyFields;
      },
      async fetchFields() {
        try {
          // 重用 sendRequest 來發送取得欄位的請求
          const res = await axios.get(`http://localhost/api/get-fields?table=${this.tableName}`);
          
          // 確認回傳的資料格式是否正確
          if (res.data && Array.isArray(res.data.field)) {
            // 將欄位資訊存入 fields，並建立空的參數對應
            this.fields = res.data.field;
            this.params = {};
            this.fields.forEach((field) => {
              this.params[field] = '';
            });
            console.log('成功取得欄位資訊:', this.fields);
            // console.log('成功取得欄位資訊:', this.requiredFields);
            alert(`成功取得資料表 "${this.tableName}" 的欄位資訊`);
          } else {
            throw new Error('回傳的資料格式不正確');
          }
        } catch (error) {
          console.error('取得欄位資訊失敗:', error);
          alert('無法取得欄位資訊，請檢查 API 路徑或資料表名稱');
        }
      },

    // 發送 API 請求的方法
    async sendRequest() {
      
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
          // headers: {
          //   Authorization: `Bearer ${token}`, 
          //   'Content-Type': 'application/json', 
          // },
        });
        this.response = res.data;
      } catch (error) {
        this.response = error.response ? error.response.data : error.message;
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

.api-test-top{
  left: 0;
  position: fixed;
  right: 0;
  top: 0;
  transform: translateZ(0);
  z-index: 300;
}
  


.api-tester {
  height: 100vh;
  padding: 20px;
  /* font-family: Arial, sans-serif; */
}

.url-input {
  width: 500px;
}

h2 {
  margin-bottom: 20px;
}

label {
  font-weight: bold;
}



button:hover {
  background-color: #0056b3;
}


</style>
