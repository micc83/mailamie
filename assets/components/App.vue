<template>
  <div class="app">

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
        <nav class="navbar">
          <message-headers :message="message"/>
          <main-menu :view.sync="view" :message="message"/>
        </nav>

        <viewer :view="view" :message="message"/>
      </template>

      <waiter v-if="!message && !loading"/>

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
import Waiter from "./Waiter";

export default {
  components: {
    MessageHeaders,
    Loader,
    MainMenu,
    Message,
    Viewer,
    Waiter
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
.app {
  display: flex;
}

.sidebar {
  max-width: 350px;
  color: var(--sidebar-text-color);
  background-color: var(--sidebar-background-color);
  border-right: 1px solid var(--sidebar-border-color);
  overflow: auto;
}

.viewer-container, .sidebar {
  height: 100vh !important;
}

.viewer-container {
  display: flex;
  width: 100%;
  flex-direction: column;
  align-content: stretch;
  overflow:hidden;
}

.navbar {
  padding-right: .5rem;
  padding-left: .5rem;
  justify-content: space-between;
  display: flex;
  background-color: var(--header-background-color);
  flex-wrap: nowrap;
  color: var(--header-text-color);
}
</style>