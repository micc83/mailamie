<template>
  <div class="headers-container">

      <div class="toggler" @click="toggleMore">
        <svg-vue v-show="showMore" icon="caret-up" fill="currentColor"></svg-vue>
        <svg-vue v-show="!showMore" icon="caret-down" fill="currentColor"></svg-vue>
      </div>

      <div class="headers">
        <div>
          <strong>{{ message.subject }}</strong>
        </div>
        <div class="header-item">
          <span class="label">To:</span> {{ message.recipients.join(', ') }}
        </div>
        <div v-if="showMore">
          <div class="header-item">
            <span class="label">Date:</span> {{ message.created_at }}
          </div>
          <div class="header-item">
            <span class="label">From:</span> {{ message.from }}
          </div>
          <div class="header-item">
            <span class="label">Cc:</span> {{ message.ccs.join(', ') }}
          </div>
        </div>
      </div>

  </div>
</template>

<script>
export default {
  props: ['message'],
  data() {
    return {
      showMore: false
    }
  },
  methods: {
    toggleMore() {
      this.showMore = !this.showMore;
    }
  }
}
</script>

<style scoped>
.headers-container {
  display: flex;
  padding: 1rem;
}

.toggler {
  display: flex;
  justify-content: center;
  flex-direction: column;
  width: 40px;
  text-align: center;
  cursor: pointer;
}

.header-item {
  font-size: 0.8rem;
}

.label {
  color: var(--light-color);
}
</style>