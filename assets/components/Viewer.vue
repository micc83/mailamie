<template>
  <div class="message-body">
    <iframe v-if="isHtml" :srcdoc="message.html"></iframe>
    <pre v-if="isText">{{ message.text }}</pre>
    <pre v-if="isRaw">{{ message.raw }}</pre>
  </div>
</template>

<script>
import {HTML, RAW, TEXT} from './MainMenu';

export default {
  props: [
    'view',
    'message'
  ],
  computed: {
    isHtml() {
      return this.view === HTML;
    },
    isText() {
      return this.view === TEXT;
    },
    isRaw() {
      return this.view === RAW;
    }
  }
}
</script>

<style scoped>
.message-body {
  height: 100%;
  padding: 1rem;
}
iframe, pre {
  width: 100%;
  height: 100%;
}
pre {
  white-space: pre-wrap;
}
</style>