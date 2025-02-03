<template>
  <aside class="widget-sidebar">
    <h3>📌 小工具</h3>
    
    <!-- 天氣資訊 -->
    <div class="widget">
      <h4>🌤 天氣</h4>

      <!-- 只有當 `isWeatherLoaded` 為 true 並且有數據時才顯示天氣資訊 -->
      <div v-if="isWeatherLoaded && weatherData.length">
        <div v-for="(forecast, index) in weatherData" :key="index" class="weather-entry">
          <div class="weather-time">
            🕒 {{ forecast.startTime }} <span>🌥</span>
          </div>
          <div class="weather-content">
            <div class="weather-info">🌦 天氣: {{ forecast.wx }}</div>
            <div class="weather-info">☁ 降雨機率: {{ forecast.pop }}%</div>
            <div class="weather-temp">
              🌡 最低: {{ forecast.minT }}°C / 最高: {{ forecast.maxT }}°C
            </div>
            <div class="weather-comfort">
              😌 舒適度: {{ forecast.ci }}
            </div>
          </div>
        </div>
      </div>

      <!-- 按鈕：按下後才顯示天氣資訊 -->
      <button @click="fetchWeather" v-if="!isWeatherLoaded">未來 36 小時</button>
    </div>


    <!-- 計算機 -->
    <div class="widget calculator">
      <h4>🧮 計算機</h4>
      <input type="text" v-model="calculatorInput" readonly class="calc-display" />
      <div class="calc-buttons">
        <button @click="appendCalc('1')">1</button>
        <button @click="appendCalc('2')">2</button>
        <button @click="appendCalc('3')">3</button>
        <button @click="appendCalc('+')">+</button>
        <button @click="appendCalc('4')">4</button>
        <button @click="appendCalc('5')">5</button>
        <button @click="appendCalc('6')">6</button>
        <button @click="appendCalc('-')">-</button>
        <button @click="appendCalc('7')">7</button>
        <button @click="appendCalc('8')">8</button>
        <button @click="appendCalc('9')">9</button>
        <button @click="appendCalc('*')">*</button>
        <button @click="clearCalc()">C</button>
        <button @click="appendCalc('0')">0</button>
        <button @click="calculate()">=</button>
        <button @click="appendCalc('/')">/</button>
      </div>
    </div>

    <!-- 即時時鐘 -->
    <div class="widget">
      <h4>⏰ 即時時鐘</h4>
      <p>{{ currentTime }}</p>
    </div>
  </aside>
  
  <div>
    <header class="api-test-top">         
      <nav class="nav-bar">
        <RouterLink to="/login" class="nav-link">登入</RouterLink> 
        <button @click="exportUsers" class="nav-link">匯出</button>
        <button @click="openFileInput" class="nav-link">匯入</button>
        <input type="file" ref="fileInput" @change="importUsers" style="display: none;" />
      </nav>
    </header>
  </div>
  <section class="api-tester">
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
    <div>
      <button @click="toggleThirdPartyFields" class="toggle-button">
        {{ showThirdPartyFields ? '隱藏三方 API 請求參數' : '顯示三方 API 請求參數' }}
      </button>
    </div>
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
    <div class="input-group">
      <label for="token" class="input-label">Token</label>
      <input id="token" type="text" v-model="token" class="token-input" placeholder="請輸入您的 Token">
    </div>
    <!-- 發送按鈕 -->
    <button :disabled="loading" @click="sendRequest" class="send-button">發送</button>


    <!-- 回應結果 -->
    <div v-if="response" class="response-section">
      <h3 class="section-title">回應結果</h3>
      <pre class="response-content">{{ response }}</pre>
    </div>
  </section>
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

      // ✅【小工具 - 即時時鐘】  
      currentTime: new Date().toLocaleTimeString(),

      // ✅【小工具 - 計算機】  
      calculatorInput: '',

      // ✅【小工具 - 天氣資訊】  
      weatherData: [], // 預設為空白，只有按下按鈕後才顯示天氣
      isWeatherLoaded: false, // 控制是否顯示天氣資訊
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
          console.log('成功取得欄位資訊:', this.fields);
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
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });

        alert('使用者資料匯入成功！');
      } catch (error) {
        console.error('匯入失敗:', error);
        alert('匯入失敗，請檢查 API 是否正常運作');
      }
    },

    // ✅【小工具 - 計算機方法】  
    appendCalc(value) {
      this.calculatorInput += value;
    },
    clearCalc() {
      this.calculatorInput = '';
    },
    calculate() {
      try {
        this.calculatorInput = new Function(`return ${this.calculatorInput}`)();
      } catch {
        alert("計算錯誤");
        this.calculatorInput = '';
      }
    },

    // ✅【小工具 - 即時時鐘】  
    updateClock() {
      this.currentTime = new Date().toLocaleTimeString();
    },

    async fetchWeather() {
      const apiUrl = 'https://opendata.cwa.gov.tw/api/v1/rest/datastore/F-C0032-001';
      const authorizationKey = 'CWA-6FFD55B9-C5A9-4FDB-8748-22CB2218F1A3';
      const locationName = '桃園市';

      try {
        const res = await axios.get(apiUrl, {
          params: {
            Authorization: authorizationKey,
            locationName: locationName,
          },
          headers: {
            Accept: 'application/json',
          },
        });

        if (res.data && res.data.records && res.data.records.location) {
          const locationData = res.data.records.location.find(loc => loc.locationName === locationName);

          if (locationData && locationData.weatherElement) {
            const weatherCondition = locationData.weatherElement.find(el => el.elementName === 'Wx');
            const rainProbability = locationData.weatherElement.find(el => el.elementName === 'PoP');
            const minTemp = locationData.weatherElement.find(el => el.elementName === 'MinT');
            const maxTemp = locationData.weatherElement.find(el => el.elementName === 'MaxT');
            const comfortIndex = locationData.weatherElement.find(el => el.elementName === 'CI');

            if (weatherCondition && rainProbability && minTemp && maxTemp && comfortIndex) {
              this.weatherData = weatherCondition.time.slice(0, 3).map((time, index) => ({
                startTime: time.startTime,
                wx: time.parameter.parameterName, // 天氣
                pop: rainProbability.time[index]?.parameter.parameterName || 'N/A', // 降雨機率
                minT: minTemp.time[index]?.parameter.parameterName || 'N/A', // 最低溫
                maxT: maxTemp.time[index]?.parameter.parameterName || 'N/A', // 最高溫
                ci: comfortIndex.time[index]?.parameter.parameterName || 'N/A', // 舒適度
              }));

              // ✅ 成功獲取數據後，將 `isWeatherLoaded` 設為 `true`
              this.isWeatherLoaded = true;
            }
          }
        }
      } catch (error) {
        console.error('取得天氣資訊失敗:', error);
      }
    },  
  },

  mounted() {
    setInterval(this.updateClock, 1000);
  }
};


</script>

<style scoped>

/*header*/
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

.api-tester {
  height: 100vh;
  padding: 20px;
  /* font-family: Arial, sans-serif; */
}

.title {
  font-size: 24px;
  color: #333;
  margin-bottom: 20px;
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

/* 整個側邊欄 */
.widget-sidebar {
  position: fixed;
  right: 0;
  top: 0;
  width: 250px;
  height: 100vh;
  background-color: #f8f9fa;
  padding: 20px;
  box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
  overflow-y: auto;
}

/* 標題 */
.widget-sidebar h3 {
  font-size: 18px;
  font-weight: bold;
  margin-bottom: 10px;
  text-align: center;
}

/* 小工具容器 */
.widget {
  background: white;
  padding: 15px;
  margin-bottom: 15px;
  border-radius: 6px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* 計算機 */
.calc-display {
  width: 100%;
  height: 40px;
  text-align: right;
  font-size: 18px;
  margin-bottom: 10px;
}

.calc-buttons {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 5px;
}

.calc-buttons button {
  padding: 10px;
  font-size: 16px;
  background: #e0e0e0;
  border: none;
  cursor: pointer;
  border-radius: 4px;
}

.calc-buttons button:hover {
  background: #d0d0d0;
}

/* 每筆天氣資訊的區塊 */
.weather-entry {
  background: #f9f9f9;
  padding: 10px;
  margin: 10px 0;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* 時間 + 圖示 */
.weather-time {
  font-weight: bold;
  font-size: 14px;
  margin-bottom: 5px;
}

/* 天氣內容 */
.weather-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 5px;
  font-size: 13px;
}

/* 調整單獨的資訊 */
.weather-info,
.weather-temp,
.weather-comfort {
  background: white;
  padding: 5px;
  border-radius: 4px;
}

button {
  all: unset; /* 清除所有瀏覽器預設樣式 */
  cursor: pointer; /* 讓按鈕仍保持點擊效果 */
}
</style>
