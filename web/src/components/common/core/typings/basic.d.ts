interface BasicOption {
    label: string;
    value: string;
}

type SelectOption = BasicOption;

type TabOption = BasicOption;

type ClassType = Array<object | string> | object | string;

export type {BasicOption, ClassType, SelectOption, TabOption};
