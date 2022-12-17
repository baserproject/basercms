<template>
  <transition name="modal" v-if="showModal" appear>
    <div class="modal modal-overlay" v-on:click.self="closeModal">
      <div class="modal-window">
        <div class="modal-content">
          <slot/>
        </div>
        <footer class="modal-footer">
          <slot name="footer">
            <button v-on:click="closeModal" type="button">Close</button>
          </slot>
        </footer>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  props: {
    scrollable: Boolean
  },
  /**
   * データ
   * @returns {{showModal: boolean}}
   */
  data: function(){
    return {
      showModal: false
    }
  },
  /**
   * Methods
   */
  methods: {
    /**
     * モーダルを開く
     * @param index
     */
    openModal: function (index) {
      this.showModal = true;
      this.$nextTick(function(){
        if(this.scrollable) {
          $(".modal-overlay").css('align-items', 'normal');
          $(".modal-window").css('overflow', 'scroll').css('display', 'grid');
        } else {
          $(".modal-overlay").css('align-items', 'center');
          $(".modal-window").css('overflow', 'hidden');
        }
        this.$emit('modal-opened', index);
      });
    },
    /**
     * モーダルを閉じる
     */
    closeModal: function () {
      this.showModal = false;
      this.$emit('modal-closed');
    }
  }
};
</script>


