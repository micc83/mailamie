<template>
  <div :class="activeClass" class="message">
    <div class="message-created-at">
      {{ message.created_at }}
    </div>
    <strong class="message-subject">
      {{ message.subject }}
    </strong>
    <small>
      <span class="label">To:</span> {{ message.recipients.join(', ') }}
    </small>
  </div>
</template>

<script>
export default {
  props: ['currentMessage', 'message'],
  computed:{
    activeClass() {
      return (this.currentMessage && (this.currentMessage.id === this.message.id))?'active':''
    }
  }
}
</script>

<style scoped>
.message-created-at {
  color: var(--sidebar-muted-color);
  font-size: 0.8rem;
}

.message-subject {
  display: block;
  color: var(--sidebar-title-color);
}

.message {
  cursor: pointer;
  border-bottom: 1px solid var(--sidebar-border-color);
  padding: 1rem;
}

.message:hover {
  background: var(--sidebar-hover-color);
}

.active {
  background: var(--sidebar-active-color) !important;
}

.label {
  color: var(--sidebar-secondary-color);
}
</style>