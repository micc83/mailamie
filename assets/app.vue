<template>

  <div class="container-fluid">
    <div class="row">
      <div class="col border-right sidebar p-0">
        <div v-for="messageItem in messages" @click="loadMessage(messageItem.id)" :key="messageItem.id"
             :class="(message && (message.id === messageItem.id))?'active':''"
             class="border-bottom p-3 message-list-item">
          <div class="float-right text-muted small">
            {{ messageItem.created_at }}
          </div>
          <strong class="d-block message-list-item-subject">{{ messageItem.subject }}</strong>
          <small><span class="text-muted">To:</span> {{ messageItem.recipients.join(', ') }}</small>
        </div>
      </div>
      <div class="col message-container p-0">
        <div v-if="message && !loading" class="h-100">
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
                    <li class="nav-item dropdown">
                      <a class="nav-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-three-dots-vertical"
                             fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd"
                                d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                        </svg>
                      </a>
                      <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <li>
                          <a class="dropdown-item" href="">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-code-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M14 1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                              <path fill-rule="evenodd" d="M6.854 4.646a.5.5 0 0 1 0 .708L4.207 8l2.647 2.646a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 0 1 .708 0zm2.292 0a.5.5 0 0 0 0 .708L11.793 8l-2.647 2.646a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708 0z"/>
                            </svg>
                            Html Version
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item" href="#">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-card-text" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M14.5 3h-13a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                              <path fill-rule="evenodd" d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8zm0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z"/>
                            </svg>
                            Text Version
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item" href="#">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-code-slash" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M4.854 4.146a.5.5 0 0 1 0 .708L1.707 8l3.147 3.146a.5.5 0 0 1-.708.708l-3.5-3.5a.5.5 0 0 1 0-.708l3.5-3.5a.5.5 0 0 1 .708 0zm6.292 0a.5.5 0 0 0 0 .708L14.293 8l-3.147 3.146a.5.5 0 0 0 .708.708l3.5-3.5a.5.5 0 0 0 0-.708l-3.5-3.5a.5.5 0 0 0-.708 0zm-.999-3.124a.5.5 0 0 1 .33.625l-4 13a.5.5 0 0 1-.955-.294l4-13a.5.5 0 0 1 .625-.33z"/>
                            </svg>
                            Raw Version
                          </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                          <a class="dropdown-item" href="#">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trophy" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path d="M3 1h10c-.495 3.467-.5 10-5 10S3.495 4.467 3 1zm0 15a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1H3zm2-1a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1H5z"/>
                              <path fill-rule="evenodd" d="M12.5 3a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-3 2a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm-6-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-3 2a3 3 0 1 1 6 0 3 3 0 0 1-6 0z"/>
                              <path d="M7 10h2v4H7v-4z"/>
                              <path d="M10 11c0 .552-.895 1-2 1s-2-.448-2-1 .895-1 2-1 2 .448 2 1z"/>
                            </svg>
                            SPAM Score
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item" href="#">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-forward-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path d="M9.77 12.11l4.012-2.953a.647.647 0 0 0 0-1.114L9.771 5.09a.644.644 0 0 0-.971.557V6.65H2v3.9h6.8v1.003c0 .505.545.808.97.557z"/>
                            </svg> Forward
                          </a>
                        </li>
                      </ul>
                    </li>
                  </ul>
                </div>
              </div>
            </nav>
          </div>
          <div class="p-3 message-body">
            <iframe :srcdoc="message.html" class="w-100 h-100"></iframe>
          </div>
        </div>
        <div v-if="!message && !loading" class="p-3">Waiting for messages...</div>
        <div v-if="loading" class="p-3">
          <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-hourglass-split text-custom"
               fill="currentColor"
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
    new WebSocket("ws://localhost:1338").onmessage = event => {
      let message = JSON.parse(event.data);
      this.messages.unshift(message)
      if (!this.message) {
        this.message = message;
      }
    };

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

.message-body {
  height: 92%;
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

.active .message-list-item-subject, .text-custom {
  color: #4b7ed6;
}

.message-list-item {
  cursor: pointer;
}

.message-list-item:hover {
  background: #fafafa;
}

.dropdown-menu svg {
  margin-right: 10px;
  color: #4b7ed6;
}
</style>