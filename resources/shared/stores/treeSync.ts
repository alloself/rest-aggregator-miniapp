import { defineStore } from 'pinia';
import { ref } from 'vue';

type UpsertEvent = {
  channel: string;
  node: Record<string, unknown>;
  sourceId: symbol;
};

type DeleteEvent = {
  channel: string;
  id: string;
  sourceId: symbol;
};

export const useTreeSyncStore = defineStore('treeSync', () => {
  const version = ref(0);
  const lastUpsert = ref<UpsertEvent | null>(null);
  const lastDelete = ref<DeleteEvent | null>(null);

  function notifyUpsert(channel: string, node: Record<string, unknown>, sourceId: symbol) {
    lastUpsert.value = { channel, node, sourceId };
    version.value++;
  }

  function notifyDelete(channel: string, id: string, sourceId: symbol) {
    lastDelete.value = { channel, id, sourceId };
    version.value++;
  }

  return {
    version,
    lastUpsert,
    lastDelete,
    notifyUpsert,
    notifyDelete,
  };
});


