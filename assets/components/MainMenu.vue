<template>
  <div class="main-menu" v-click-outside="onClickOutside">
    <div>
      <div class="dropdown-menu-container" v-if="message.attachments.length">
        <a class="nav-link" href="#" @click.prevent="toggleAttachments">
          <svg-vue icon="attachments" fill="currentColor"></svg-vue>
        </a>
        <ul class="dropdown-menu" v-show="showAttachments">
          <li v-for="attachment in message.attachments">
            <a class="dropdown-item" :href="attachment.url" target="_blank" @click="toggleAttachments">
              <svg-vue icon="download" fill="currentColor"></svg-vue>
              {{ attachment.name }}
            </a>
          </li>
        </ul>
      </div>
      <div class="dropdown-menu-container">
        <a class="nav-link" href="#" @click.prevent="toggleMenu">
          <svg-vue icon="menu" fill="currentColor"></svg-vue>
        </a>
        <ul class="dropdown-menu" v-show="showMenu">
          <li>
            <a class="dropdown-item" href="#" @click.prevent="setView(HTML)" v-if="view !== HTML">
              <svg-vue icon="html" fill="currentColor"></svg-vue>
              Html Version
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="#" @click.prevent="setView(TEXT)" v-if="view !== TEXT">
              <svg-vue icon="text" fill="currentColor"></svg-vue>
              Text Version
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="#" @click.prevent="setView(RAW)" v-if="view !== RAW">
              <svg-vue icon="raw" fill="currentColor"></svg-vue>
              Raw Version
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
export const HTML = 'html';
export const RAW = 'raw';
export const TEXT = 'text';

Vue.directive('click-outside', {
  bind: function (el, binding, vnode) {
    el.clickOutsideEvent = function (event) {
      if (!(el == event.target || el.contains(event.target))) {
        vnode.context[binding.expression](event);
      }
    };
    document.body.addEventListener('click', el.clickOutsideEvent)
  },
  unbind: function (el) {
    document.body.removeEventListener('click', el.clickOutsideEvent)
  },
});

export default {
  props: ['view', 'message'],
  created() {
    this.HTML = HTML;
    this.RAW = RAW;
    this.TEXT = TEXT;
  },
  data() {
    return {
      showMenu: false,
      showAttachments: false
    }
  },
  methods: {
    setView(view) {
      this.showMenu = false;
      this.$emit('update:view', view);
    },
    toggleMenu(){
      this.showMenu = !this.showMenu;
      this.showAttachments = false;
    },
    toggleAttachments(){
      this.showAttachments = !this.showAttachments;
      this.showMenu = false;
    },
    onClickOutside(){
      this.showMenu = false;
      this.showAttachments = false;
    }
  }
}
</script>

<style scoped>
.main-menu {
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.nav-link {
  display: inline-block;
  padding: 10px;
  color: var(--header-link-color);
}

.dropdown-menu-container {
  display: inline-block;
}

.dropdown-menu {
  padding-left: 0;
  position: absolute;
  width: 200px;
  top: 59px;
  right: 0;
  background: var(--dropdown-background-color);
  border-left: 1px solid var(--dropdown-border-color);
  border-right: 1px solid var(--dropdown-border-color);
}

.dropdown-menu li {
  list-style: none;
  padding: 0;
  line-height: 1rem;
}

.dropdown-item {
  text-decoration: none;
  color: var(--dropdown-link-color);
  padding: 0.6rem;
  border-bottom: 1px solid var(--dropdown-border-color);
  display: block;
}

.dropdown-item:hover {
  background-color: var(--dropdown-hover-color);
}

.dropdown-menu svg {
  margin: 0 0.5rem 0 0.2rem;
  color: var(--dropdown-icon-color);
  vertical-align: middle;
}
</style>