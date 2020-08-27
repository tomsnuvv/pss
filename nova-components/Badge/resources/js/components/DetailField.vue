<template>
    <panel-item :field="field">
        <template slot="value">
            <span
                v-if="value"
                v-bind:class="className"
                class="rounded-full uppercase px-2 py-1 text-xs font-bold mr-3 badge-default">
                {{ value }}
            </span>
            <span
                v-else
                class="text-80">
                -
            </span>
        </template>
    </panel-item>
</template>

<script>
export default {
    props: ['field'],
    computed: {
      className: function() {
        return this.value ? this.field.classMap[this.value] : {}
      },
      default: function() {
        return this.field.default || ''
      },
      value: function() {
        return this.field.value ? this.field.value : this.default
      },
    }
}
</script>
