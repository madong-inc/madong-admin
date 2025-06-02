import {StoreDefinition} from 'pinia';

declare module 'pinia' {
    export function acceptHMRUpdate(
        initialUseStore: any | StoreDefinition,
        hot: any,
    ): (newModule: any) => any;
}

export {};
