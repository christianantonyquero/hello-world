<template>
  <div v-if="errors.length > 0" class="alert alert-danger pl-0" role="alert">
    <ul class="mb-0">
      <li v-for="error in errors">{{ error }}</li>
    </ul>
  </div>
</template>

<script>
export default {
  props: ['errors'],
  methods: {

  },
  computed: {

  }
};
</script>
