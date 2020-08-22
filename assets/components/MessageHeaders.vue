<template>
  <ul class="navbar-nav">
    <li class="nav-item d-flex">
      <div class="d-flex justify-content-center flex-column">
        <div class="more-link" @click="toggleMore">
          <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-caret-down-fill"
               v-if="!showMore"
               fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
          </svg>
          <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-caret-up-fill"
               v-else
               fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M7.247 4.86l-4.796 5.481c-.566.647-.106 1.659.753 1.659h9.592a1 1 0 0 0 .753-1.659l-4.796-5.48a1 1 0 0 0-1.506 0z"/>
          </svg>
        </div>
      </div>
      <div>
        <div>
          <strong>{{ message.subject }}</strong>
        </div>
        <div class="small">
          <span class="text-muted">To:</span> {{ message.recipients.join(', ') }}
        </div>
        <div v-if="showMore">
          <div class="small">
            <span class="text-muted">From:</span> {{ message.from }}
          </div>
        </div>
      </div>
    </li>
  </ul>
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

<style>
.more-link {
  width: 40px;
  text-align: center;
  cursor: pointer;
}
</style>