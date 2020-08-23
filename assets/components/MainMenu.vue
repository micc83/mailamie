<template>
  <div class="main-menu">
    <div>
      <a class="nav-link" href="#">
        <svg-vue icon="attachments" fill="currentColor"></svg-vue>
      </a>
      <div class="dropdown-menu-container">
        <a class="nav-link" href="#" @click.prevent="showMenu = !showMenu">
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

export default {
  props: ['view'],
  created() {
    this.HTML = HTML;
    this.RAW = RAW;
    this.TEXT = TEXT;
  },
  data() {
    return {
      showMenu: false
    }
  },
  methods: {
    setView(view) {
      this.showMenu = false;
      this.$emit('update:view', view);
    },
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
  color: var(--white);
}

.dropdown-menu-container {
  display: inline-block;
}

.dropdown-menu {
  padding-left: 0;
  position: absolute;
  width: 200px;
  right: 0;
  background: var(--background-color);
  border-left: 1px solid var(--border-color);
  border-right: 1px solid var(--border-color);
}

.dropdown-menu li {
  list-style: none;
  padding: 0;
  line-height: 1rem;
}

.dropdown-item {
  text-decoration: none;
  color: var(--black);
  padding: 0.6rem;
  border-bottom: 1px solid var(--border-color);
  display: block;
}

.dropdown-menu svg {
  margin: 0 0.5rem 0 0.2rem;
  color: #4b7ed6;
  vertical-align: middle;
}
</style>