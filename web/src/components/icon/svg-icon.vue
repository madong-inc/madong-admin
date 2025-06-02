<template>
  <div v-if="svgContent" v-html="svgContent" class="svg-icon" :style="iconStyle"></div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted, computed } from "vue";

export default defineComponent({
  name: "SvgIcon",
  props: {
    src: {
      type: String,
      required: true,
    },
    width: {
      type: String,
      default: "100%", // 默认宽度
    },
    height: {
      type: String,
      default: "auto", // 默认高度
    },
    color: {
      type: String,
      default: "currentColor", // 默认颜色
    },
  },
  setup(props) {
    const svgContent = ref<string | null>(null);

    const fetchSvg = async () => {
      try {
        const response = await fetch(props.src);
        if (!response.ok) {
          throw new Error("Failed to load SVG");
        }
        svgContent.value = await response.text();
      } catch (error) {
        console.error(error);
        svgContent.value = null; // 或者可以设置一个错误状态
      }
    };

    onMounted(() => {
      fetchSvg();
    });

    // 计算样式
    const iconStyle = computed(() => ({
      width: props.width,
      height: props.height,
      fill: props.color, // 设置填充颜色
    }));

    return {
      svgContent,
      iconStyle,
    };
  },
});
</script>

<style scoped>
.svg-icon {
  display: inline-block; /* 确保 SVG 按需显示 */
}
</style>
