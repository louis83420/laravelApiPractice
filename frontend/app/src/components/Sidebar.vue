<template>
    <aside class="sidebar">
      <h3> 功能選單</h3>
      <nav>
        <router-link to="/" class="nav-item">API 測試</router-link>
        <router-link to="/register" class="nav-item">憑證註冊</router-link>
      </nav>
    </aside>
  </template>
  
  <style scoped>
  .sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 220px;
    height: 100vh;
    background-color: #f8f9fa;
    padding: 20px;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    overflow-y: auto;
  }
  
  .sidebar h3 {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 15px;
    text-align: center;
  }
  
  .nav-item {
    display: block;
    padding: 10px;
    margin: 5px 0;
    color: #333;
    text-decoration: none;
    background: white;
    border-radius: 6px;
    text-align: center;
    transition: 0.3s;
  }
  
  .nav-item:hover {
    background-color: #ddd;
  }
  </style>
  