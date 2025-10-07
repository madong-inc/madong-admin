import { mitt } from '#/components/core/utils';

type Events = {
  updateProfile: void;
};

export const emitter = mitt<Events>();
