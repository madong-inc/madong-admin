export interface OptionItem {
    color?: string;
    label: string;
    value: number | string;
  }
  
  export interface OptionsTagProps {
    options: OptionItem[];
    value: number | string;
  }
  
  // 如果需要定义emits类型
  export interface OptionsTagEmits {
    (e: 'update:value', value: number | string): void;
  }