<template>
  <div class="d-flex">

    <!-- Sidebar -->
    <div class="sidebar">
      <message v-for="messageItem in messages"
               @click.native="loadMessage(messageItem.id)"
               :message="messageItem"
               :current-message="message"
               :key="messageItem.id"/>
    </div>

    <!-- Viewer -->
    <div class="viewer-container">

      <template v-if="message && !loading">
        <nav class="navbar navbar-expand-lg">
          <message-headers :message="message"/>
          <main-menu :view.sync="view"/>
        </nav>

        <viewer :view="view" :message="message"/>
      </template>

      <div v-if="!message && !loading" class="p-3">
        Waiting for messages...
      </div>

      <loader v-if="loading"/>

    </div>
  </div>
</template>

<script>
import Loader from "./Loader"
import MainMenu, {HTML} from "./MainMenu"
import MessageHeaders from "./MessageHeaders";
import Message from "./Message"
import Viewer from "./Viewer";

export default {
  components: {
    MessageHeaders,
    Loader,
    MainMenu,
    Message,
    Viewer
  },
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
      view: HTML
    }
  },
  methods: {
    async loadMessage(id) {
      this.loading = true;
      this.message = (await axios.get(`/api/messages/${id}`)).data;
      this.loading = false;
    }
  }
}
</script>

<style>
.d-flex {
  display: flex;
}

.sidebar {
  max-width: 350px;
  border-right: 1px solid #dee2e6;
}

.viewer-container, .sidebar {
  height: 100vh !important;
  overflow: auto;
}

.viewer-container {
  display: flex;
  width: 100%;
  flex-direction: column;
  align-content: stretch;
}

.navbar {
  padding-right: .5rem !important;
  padding-left: .5rem !important;
  width: 100%;
  justify-content: space-between;
  display: flex;
  background-color: #f8f9fa !important;
  flex-wrap: nowrap !important;
}
</style>