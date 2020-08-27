<template>
    <panel-item :field="field">
        <template slot="value">
            <span :class="this.field.className" class="no-wrap">{{ this.field.expiration }}</span>
        </template>
    </panel-item>
</template>

<script>
export default {
    props: ['field'],
}
</script>
