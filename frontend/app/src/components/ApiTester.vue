<template>
  <div class="api-tester">
    <h2>API 測試用</h2>
    <div>
      <label for="url">API URL:</label>
      <input v-model="url" type="text" placeholder="輸入API的URL" />
    </div>
    <div>
      <label for="method">方法</label>
      <select v-model="method">
        <option value="GET" class="get-option">GET</option>
        <option value="POST" class="post-option">POST</option>
        <option value="PUT" class="put-option">PUT</option>
        <option value="DELETE" class="delete-option">DELETE</option>
      </select>
    </div>
    <div>
      <!-- <label for="param">參數</label>參數未完成 -->
    </div>
  </div>
  
</template>

<style scoped>
.custom-select select {
  appearance: none;
  border: none;
  padding: 10px;
  font-size: 16px;
  border-radius: 5px;
  cursor: pointer;
  width: 100%;
}


.get-option {
  background-color: #007bff;
  color: white;
}


.post-option {
  background-color: #28a745;
  color: white;
}


.put-option {
  background-color: #ffc107;
  color: white;
}


.delete-option {
  background-color: #dc3545;
  color: white;
}


select:focus {
  outline: none;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}
</style>
