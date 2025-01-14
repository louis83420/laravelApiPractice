import axios from 'axios';

// 設定後端 API 的基礎路徑
const apiClient = axios.create({
    baseURL: 'http://127.0.0.1/api', // Laravel API 的基礎 URL
    headers: {
        'Content-Type': 'application/json'
    }
});

export default apiClient;
