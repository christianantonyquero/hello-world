<template>
    <div class="dropdown"
         :class="{'is-active': isActive}">
    <div class="dropdown-trigger">
        <button class="button" aria-haspopup="true" aria-controls="dropdown-menu"
            @click="toggle">
            <span><slot name="trigger-text"></slot></span>
            <span class="icon is-small">
                <i class="fas fa-angle-down" aria-hidden="true"></i>
            </span>
        </button>
    </div>
    <div class="dropdown-menu" id="dropdown-menu" role="menu">
        <div class="dropdown-content">
            <slot></slot>
        </div>
    </div>
</div>
</template>

<script>
export default {
    name: "Dropdown",
    data() {
        return {
            isActive: false,
        }
    },
    methods: {
        toggle() {
            this.isActive = !this.isActive;
        }
    }
}
</script>