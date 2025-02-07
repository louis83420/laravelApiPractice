<template>
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
</template>

<script>
export default {
    data() {
        return {
        calculatorInput: '',
        };
    },
    methods: {
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
    },
};
</script>

<style scoped>
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
</style>