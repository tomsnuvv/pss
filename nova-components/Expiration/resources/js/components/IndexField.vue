<template>
    <span :class="this.field.className" class="no-wrap">{{ this.field.expiration }}</span>
</template>

<script>
export default {
    props: ['field'],
}
</script>
