<template>
    <div class="widget">
        <h4>🌤 天氣</h4>
        <div v-if="isWeatherLoaded && weatherData.length">
        <div v-for="(forecast, index) in weatherData" :key="index" class="weather-entry">
            <div class="weather-time">🕒 {{ forecast.startTime }} <span>🌥</span></div>
            <div class="weather-content">
            <div class="weather-info">🌦 天氣: {{ forecast.wx }}</div>
            <div class="weather-info">☁ 降雨機率: {{ forecast.pop }}%</div>
            <div class="weather-temp">🌡 最低: {{ forecast.minT }}°C / 最高: {{ forecast.maxT }}°C</div>
            <div class="weather-comfort">😌 舒適度: {{ forecast.ci }}</div>
            </div>
        </div>
        </div>
        <button @click="fetchWeather" v-if="!isWeatherLoaded">未來 36 小時</button>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
        weatherData: [],
        isWeatherLoaded: false,
        };
    },
    methods: {
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
                    wx: time.parameter.parameterName,
                    pop: rainProbability.time[index]?.parameter.parameterName || 'N/A',
                    minT: minTemp.time[index]?.parameter.parameterName || 'N/A',
                    maxT: maxTemp.time[index]?.parameter.parameterName || 'N/A',
                    ci: comfortIndex.time[index]?.parameter.parameterName || 'N/A',
                }));

                this.isWeatherLoaded = true;
                }
            }
            }
        } catch (error) {
            console.error('取得天氣資訊失敗:', error);
        }
        },
    },
};
</script>

<style scoped>
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
</style>