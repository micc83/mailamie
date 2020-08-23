<template>
  <div class="waiter">
    <svg-vue icon="waiting" fill="currentColor"></svg-vue>
    <div class="waiting-text">Waiting for incoming emails...</div>
  </div>
</template>

<style scoped>
.waiter {
  display: flex;
  justify-content: center;
  flex-direction: column;
  text-align: center;
  height: 100%;
}

svg {
  margin: 0 auto 20px;
  color: var(--light-color);
  height: 100px;
  width: 100px;
}

.waiting-text {
  color: var(--light-color);
  font-size: 18px;
  font-weight: 600;
}
</style>