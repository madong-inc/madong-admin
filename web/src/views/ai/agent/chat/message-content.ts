import type { PropType } from 'vue';

import type { ChatMessage } from './types/scheam';

import { defineComponent, h } from 'vue';

export const MessageContent = defineComponent({
  name: 'MessageContent',
  props: {
    message: {
      type: Object as PropType<ChatMessage>,
      required: true,
    },
  },
  emits: ['select-example'],
  setup(props, { emit }) {
    return () => {
      const content = props.message.content;
      if (props.message.type === 'ai' && props.message.examples) {
        const lines = content.split('\n');
        const welcomeText = lines
          .filter((line) => !line.startsWith('•'))
          .join('\n');
        const examples = lines.filter((line) => line.startsWith('•'));

        return h('div', { class: 'message-text' }, [
          // 欢迎文本
          h('div', { class: 'welcome-text' }, welcomeText),
          // 示例问题容器
          h(
            'div',
            { class: 'examples-container' },
            examples.map((line) =>
              h('div', { class: 'example-item' }, [
                h(
                  'a',
                  {
                    class: 'example-text',
                    href: 'javascript:void(0)',
                    onClick: (e) => {
                      e.preventDefault();
                      emit('select-example', line.slice(2).trim());
                    },
                  },
                  line.slice(2).trim(),
                ),
              ]),
            ),
          ),
        ]);
      }
      return h('div', content);
    };
  },
});
