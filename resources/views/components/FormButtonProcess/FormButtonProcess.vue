<template>
    <div>
        <input
            class="FormButtonProcess"
            :class="isclasses"
            type="submit"
            :value="processing ? processingtitle : title"
            @click.once="onClick"
            :disabled="processing"
        />
    </div>
</template>

<script>
export default {
    props: {
        isclasses: { default: '' },
        title: { default: '' },
        processingtitle: { default: '' },
        id: { default: '' }
    },

    data: () => ({
        processing: false
    }),

    methods: {
        onClick(e) {
            e.preventDefault()
            if (this.processing === false) {
                this.processing = true
                document.getElementById(this.id).submit()
            }
        }
    }
}
</script>
