<template>
  <div class="loader">
    <svg-vue icon="loading" fill="currentColor"></svg-vue>
    <div class="loading-text">Loading...</div>
  </div>
</template>

<style scoped>
.loader {
  display: flex;
  justify-content: center;
  flex-direction: column;
  text-align: center;
  height: 100%;
}

svg {
  margin: 0 auto 20px;
  color: var(--waiting-color);
  height: 100px;
  width: 100px;
}

.loading-text {
  color: var(--waiting-color);
  font-size: 18px;
  font-weight: 600;
}
</style>