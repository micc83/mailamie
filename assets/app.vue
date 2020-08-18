<template>

  <div class="container-fluid">
    <div class="row">
      <div class="col border-right sidebar p-0">
        <div v-for="messageItem in messages" @click="loadMessage(messageItem.id)" :key="messageItem.id"
             :class="(message && (message.id === messageItem.id))?'active':''"
             class="border-bottom p-3 message-list-item">
          <strong class="d-block message-list-item-subject">{{ messageItem.subject }}</strong>
          <small><span class="text-muted">To:</span> {{ messageItem.recipients.join(', ') }}</small>
        </div>
      </div>
      <div class="col  message-container p-0">
        <div v-if="message && !loading">
          <div class="sticky-top">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
              <div class="container-fluid">
                <div class="d-flex justify-content-between w-100" id="navbarNav">
                  <ul class="navbar-nav">
                    <li class="nav-item d-flex">
                      <div class="d-flex justify-content-center flex-column">
                        <div style="width: 40px" class="more-link" @click="toggleMore">
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
                  <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                      <a class="nav-link" href="#">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-paperclip" fill="currentColor"
                             xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd"
                                d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0V3z"/>
                        </svg>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-three-dots-vertical"
                             fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd"
                                d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                        </svg>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </nav>
          </div>
          <div>
            <div class="p-3 message-body">
              <div v-html="message.html"></div>
            </div>
          </div>
        </div>
        <div v-if="!message && !loading" class="p-3">Waiting for messages...</div>
        <div v-if="loading" class="p-3">
          <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-hourglass-split text-muted" fill="currentColor"
               xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                  d="M2.5 15a.5.5 0 1 1 0-1h1v-1a4.5 4.5 0 0 1 2.557-4.06c.29-.139.443-.377.443-.59v-.7c0-.213-.154-.451-.443-.59A4.5 4.5 0 0 1 3.5 3V2h-1a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1h-1v1a4.5 4.5 0 0 1-2.557 4.06c-.29.139-.443.377-.443.59v.7c0 .213.154.451.443.59A4.5 4.5 0 0 1 12.5 13v1h1a.5.5 0 0 1 0 1h-11zm2-13v1c0 .537.12 1.045.337 1.5h6.326c.216-.455.337-.963.337-1.5V2h-7zm3 6.35c0 .701-.478 1.236-1.011 1.492A3.5 3.5 0 0 0 4.5 13s.866-1.299 3-1.48V8.35zm1 0c0 .701.478 1.236 1.011 1.492A3.5 3.5 0 0 1 11.5 13s-.866-1.299-3-1.48V8.35z"/>
          </svg>
          Wait please...
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  async created() {
    this.messages = (await axios.get('/api/messages')).data;
    if (this.messages[0]) {
      await this.loadMessage(this.messages[0].id)
    }
    this.loading = false;
  },
  data() {
    return {
      messages: [],
      message: null,
      loading: true,
      showMore: false
    }
  },
  methods: {
    async loadMessage(id) {
      this.loading = true;
      this.message = (await axios.get(`/api/messages/${id}`)).data;
      this.loading = false;
    },
    toggleMore() {
      this.showMore = !this.showMore;
    }
  }
}
</script>

<style>
.sidebar {
  max-width: 350px;
}

.message-container, .sidebar {
  height: 100vh !important;
  overflow: auto;
}

.more-link {
  cursor: pointer;
}

.message-list-item.active {
  background: aliceblue !important;
}

.active .message-list-item-subject {
  color: #4b7ed6;
}

.message-list-item {
  cursor: pointer;
}

.message-list-item:hover {
  background: #fafafa;
}
</style>