<template>
    <div class="NewsletterComposer" :class="isclasses">
        <div class="NewsletterComposer__cheatsheet">
            <p class="Body" v-html="cheatsheet"></p>
        </div>

        <div class="NewsletterComposer__item" v-for="(item, key) in items" :key="key">
            <div class="NewsletterComposer__item-left">
                <textarea
                    name="body[]"
                    class="FormTextarea__textarea"
                    :placeholder="content_placeholder"
                    v-model="item.body"
                ></textarea>

                <div class="NewsletterComposer__visible">
                    <div class="NewsletterComposer__visibleLeft">
                        <input
                            type="text"
                            :value="item.visible_from"
                            name="visible_from[]"
                            class="FormTextfield__input"
                            :placeholder="visible_from_placeholder"
                        />
                    </div>
                    <div class="NewsletterComposer__visibleRight">
                        <input
                            type="text"
                            :value="item.visible_to"
                            name="visible_to[]"
                            class="FormTextfield__input"
                            :placeholder="visible_to_placeholder"
                        />
                    </div>
                </div>
            </div>
            <div class="NewsletterComposer__item-right">
                <button
                    @click="cloneItem(item, key)"
                    type="button"
                    class="NewsletterComposer__button NewsletterComposer__button-clone"
                >
                    +
                </button>
                <button
                    @click="removeItem(key)"
                    type="button"
                    class="NewsletterComposer__button NewsletterComposer__button-remove"
                >
                    ×
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        isclasses: { default: '' },
        letter_contents: { default: [] },
        content_placeholder: { default: '' },
        visible_from_placeholder: { default: '' },
        visible_to_placeholder: { default: '' },
        cheatsheet: { default: '' }
    },

    data() {
        return {
            items: this.letter_contents
        }
    },

    methods: {
        cloneItem: function(item, key) {
            var new_item = {}
            new_item.body = ''
            new_item.visible_from = ''
            new_item.visible_to = ''

            this.items.splice(parseInt(key + 1), 0, new_item)
        },

        removeItem: function(key) {
            this.items.splice(key, 1)
        }
    }
}
</script>
