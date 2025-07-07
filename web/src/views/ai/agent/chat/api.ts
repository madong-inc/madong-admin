import type { ChatMessage, ChatSession } from './types/scheam';


// 模拟会话列表数据
export const mockSessionList: ChatSession[] = [
  {
    id: '1',
    title: '关于Vue3的讨论',
    lastMessage: '请问Vue3的组合式API有什么优势？',
    timestamp: Date.now() - 3_600_000,
  },
  // {
  //   id: '2',
  //   title: '算法问题求助',
  //   lastMessage: '如何实现一个快速排序？',
  //   timestamp: Date.now() - 7_200_000,
  // },
  // {
  //   id: '3',
  //   title: '算法问题求助',
  //   lastMessage: '如何实现一个快速排序？',
  //   timestamp: Date.now() - 7_200_000,
  // },
  // {
  //   id: '4',
  //   title: '算法问题求助',
  //   lastMessage: '如何实现一个快速排序？',
  //   timestamp: Date.now() - 7_200_000,
  // },
  // {
  //   id: '5',
  //   title: '算法问题求助',
  //   lastMessage: '如何实现一个快速排序？',
  //   timestamp: Date.now() - 7_200_000,
  // },
  // {
  //   id: '6',
  //   title: '算法问题求助',
  //   lastMessage: '如何实现一个快速排序？',
  //   timestamp: Date.now() - 7_200_000,
  // },
  // {
  //   id: '7',
  //   title: '算法问题求助',
  //   lastMessage: '如何实现一个快速排序？',
  //   timestamp: Date.now() - 7_200_000,
  // },
  // {
  //   id: '8',
  //   title: '算法问题求助',
  //   lastMessage: '如何实现一个快速排序？',
  //   timestamp: Date.now() - 7_200_000,
  // },
  // {
  //   id: '9',
  //   title: '算法问题求助',
  //   lastMessage: '如何实现一个快速排序？',
  //   timestamp: Date.now() - 7_200_000,
  // },
  // {
  //   id: '10',
  //   title: '算法问题求助',
  //   lastMessage: '如何实现一个快速排序？',
  //   timestamp: Date.now() - 7_200_000,
  // },
  // {
  //   id: '11',
  //   title: '算法问题求助',
  //   lastMessage: '如何实现一个快速排序？',
  //   timestamp: Date.now() - 7_200_000,
  // },
];

// 模拟会话消息数据
export const mockMessagesMap: Record<string, ChatMessage[]> = {
  '1': [
    {
      id: '1-1',
      type: 'user',
      content: '请问Vue3的组合式API有什么优势？',
      time: new Date(Date.now() - 3_600_000).toLocaleTimeString(),
    },
    {
      id: '1-2',
      type: 'ai',
      content:
        'Vue3的组合式API（Composition API）带来了以下优势：\n1. 更好的代码组织\n2. 更好的类型推导\n3. 更好的逻辑复用\n4. 更小的打包体积',
      time: new Date(Date.now() - 3_590_000).toLocaleTimeString(),
    },
  ],
  '2': [
    {
      id: '2-1',
      type: 'user',
      content: '如何实现一个快速排序？',
      time: new Date(Date.now() - 7_200_000).toLocaleTimeString(),
    },
    {
      id: '2-2',
      type: 'ai',
      content:
        '快速排序的基本思路是：\n1. 选择基准值\n2. 将数组分区\n3. 递归排序子区间',
      time: new Date(Date.now() - 7_190_000).toLocaleTimeString(),
    },
  ],
};

// 模拟API请求
export const mockApi = {
  // 获取会话列表
  getSessionList: () => {
    return Promise.resolve(mockSessionList);
  },

  // 获取会话消息
  getSessionMessages: (sessionId: string) => {
    return Promise.resolve(mockMessagesMap[sessionId] || []);
  },

  // 修改创建新会话方法
  createSession: (title: string = '新的对话') => {
    const newSession: ChatSession = {
      id: Date.now().toString(),
      title,
      timestamp: Date.now(),
    };
    return Promise.resolve(newSession);
  },

  // 更新会话标题
  updateSessionTitle: (sessionId: string, title: string) => {
    return Promise.resolve({ sessionId, title });
  },

  // 删除会话
  deleteSession: (sessionId: string) => {
    return Promise.resolve({ success: true });
  },

  // 发送消息
  sendMessage: (sessionId: string, content: string) => {
    const message: ChatMessage = {
      id: Date.now().toString(),
      type: 'user',
      content,
      time: new Date().toLocaleTimeString(),
    };
    return Promise.resolve(message);
  },

  // 获取AI回复
  getAiResponse: (sessionId: string, content: string) => {
    const message: ChatMessage = {
      id: Date.now().toString(),
      type: 'ai',
      content: `这是对 "${content}" 的回复`,
      time: new Date().toLocaleTimeString(),
    };
    return Promise.resolve(message);
  },
};
