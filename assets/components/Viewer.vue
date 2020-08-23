<template>
  <div class="message-body">
    <iframe v-if="view === HTML" :srcdoc="message.html"></iframe>
    <pre v-if="view === TEXT">{{ message.text }}</pre>
    <pre v-if="view === RAW">{{ message.raw }}</pre>
  </div>
</template>

<script>
import {HTML, RAW, TEXT} from './MainMenu';

export default {
  created() {
    this.HTML = HTML;
    this.RAW = RAW;
    this.TEXT = TEXT;
  },
  props: [
    'view',
    'message'
  ]
}
</script>

<style scoped>
.message-body {
  height: 100%;
  padding: 1rem;
}
iframe {
  border: none;
}
iframe, pre {
  width: 100%;
  height: 100%;
}
pre {
  white-space: pre-wrap;
}
</style>