import { mitt } from '#/components/common/utils';

type Events = {
  updateProfile: void;
};

export const emitter = mitt<Events>();
