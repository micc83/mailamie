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
  float: right;
  color: #6c757d;
  font-size: 0.8rem;
}

.message-subject {
  display: block;
  color: #4b7ed6;
}

.message {
  cursor: pointer;
  border-bottom: 1px solid #dee2e6;
  padding: 1rem;
}

.message:hover {
  background: #fafafa;
}

.active {
  background: aliceblue !important;
}

.active .message-subject {
  color: #4b7ed6;
}

.label {
  color: #6c757d;
}
</style>