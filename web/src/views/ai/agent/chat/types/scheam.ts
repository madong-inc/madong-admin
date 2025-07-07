export interface ChatMessage {
  id: string;
  type: 'ai' | 'user';
  content: string;
  time: string;
  examples?: string[];
}

export interface ChatSession {
  id: string;
  title: string;
  messages?: ChatMessage[] | undefined;
  lastMessage?: string | undefined;
  timestamp?: number;
  isEditing?: boolean;
}
export interface ChatProps {
  // 配置项
  config?: {
    contextLength?: number;
    modelName?: string;
    placeholder?: string;
  };
  // 事件回调
  onMessage?: (message: string) => Promise<void>;
  onSessionCreate?: (title: string) => Promise<void>;
  onSessionDelete?: (sessionId: string) => Promise<void>;
  onSessionUpdate?: (sessionId: string, title: string) => Promise<void>;
}
