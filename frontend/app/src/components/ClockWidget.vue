<template>
    <div class="widget">
      <h4>⏰ 即時時鐘</h4>
      <p>{{ currentTime }}</p>
    </div>
</template>

<script>
export default {
    data() {
        return {
        currentTime: new Date().toLocaleTimeString(),
        };
    },
    mounted() {
        setInterval(this.updateClock, 1000);
    },
    methods: {
        updateClock() {
        this.currentTime = new Date().toLocaleTimeString();
        },
    },
};
</script>

<style scoped>
/* 樣式與原本相同 */
</style>