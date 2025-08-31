<template>
  <div class="working-hours" @click="openDialog">
    <div v-for="group in groupedSchedule" :key="group.days" class="working-hours__item">
      <span class="working-hours__days">{{ `${group.days}: ` }}</span>
      <span class="working-hours__time">{{ group.time }}</span>
    </div>
  </div>

  <Dialog
    v-model:visible="isDialogVisible"
    :style="{ width: '260px' }"
    modal
    header="Время работы"
    class="working-hours-dialog"
  >
    <div class="working-hours-dialog__content">
      <div v-for="day in detailedSchedule" :key="day.dayKey" class="working-hours-dialog__item">
        <span class="working-hours-dialog__day">{{ day.dayName }}</span>
        <span class="working-hours-dialog__time">{{ day.time }}</span>
      </div>
    </div>
  </Dialog>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';

interface WorkingDay {
  startTime: string;
  endTime: string;
}

interface WorkingHours {
  [key: string]: WorkingDay;
}

interface ScheduleGroup {
  days: string;
  time: string;
}

interface Props {
  workingHours: WorkingHours | null | undefined;
}

const props = defineProps<Props>();

// Состояние модального окна
const isDialogVisible = ref(false);

// Функция открытия диалога
const openDialog = () => {
  isDialogVisible.value = true;
};

// Маппинг дней недели на русские сокращения
const dayNamesMap: Record<string, string> = {
  monday: 'пн',
  tuesday: 'вт',
  wednesday: 'ср',
  thursday: 'чт',
  friday: 'пт',
  saturday: 'сб',
  sunday: 'вс',
};

// Порядок дней недели
const dayOrder = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

// Обратный маппинг для поиска английского названия дня по русскому сокращению
const reverseNameMap: Record<string, string> = {
  пн: 'monday',
  вт: 'tuesday',
  ср: 'wednesday',
  чт: 'thursday',
  пт: 'friday',
  сб: 'saturday',
  вс: 'sunday',
};

// Функция для извлечения первого дня недели из строки дней
const getFirstDayFromGroup = (daysString: string): string => {
  // Если это диапазон (пн-пт), берем первую часть
  if (daysString.includes('-')) {
    const firstDayRu = daysString.split('-')[0];
    return reverseNameMap[firstDayRu] || 'monday';
  }

  // Если это список через запятую (пн, ср), берем первый элемент
  if (daysString.includes(',')) {
    const firstDayRu = daysString.split(',')[0].trim();
    return reverseNameMap[firstDayRu] || 'monday';
  }

  // Если это одиночный день
  return reverseNameMap[daysString] || 'monday';
};

// Детализированное расписание для модального окна
const detailedSchedule = computed(() => {
  if (!props.workingHours) return [];

  return dayOrder.map((dayKey) => {
    const dayName = dayNamesMap[dayKey];
    const schedule = props.workingHours?.[dayKey];

    return {
      dayKey,
      dayName,
      time: schedule ? `${schedule.startTime} - ${schedule.endTime}` : 'выходной',
    };
  });
});

const groupedSchedule = computed((): ScheduleGroup[] => {
  if (!props.workingHours) return [];

  // Разделяем дни на будни и выходные
  const weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
  const weekendDays = ['saturday', 'sunday'];

  // Группируем дни по времени работы, разделяя будни и выходные
  const timeGroups: Record<string, string[]> = {};

  // Обрабатываем рабочие дни
  Object.entries(props.workingHours).forEach(([day, schedule]) => {
    const timeKey = `${schedule.startTime}-${schedule.endTime}`;
    let groupKey: string;

    // Определяем группу: будни или выходные
    if (weekdays.includes(day)) {
      groupKey = `weekdays-${timeKey}`;
    } else if (weekendDays.includes(day)) {
      groupKey = `weekend-${timeKey}`;
    } else {
      groupKey = timeKey; // fallback для неожиданных дней
    }

    if (!timeGroups[groupKey]) {
      timeGroups[groupKey] = [];
    }
    timeGroups[groupKey].push(day);
  });

  // Находим нерабочие дни
  const workingDays = Object.keys(props.workingHours);
  const nonWorkingWeekdays = weekdays.filter((day) => !workingDays.includes(day));
  const nonWorkingWeekends = weekendDays.filter((day) => !workingDays.includes(day));

  // Добавляем нерабочие будни как отдельную группу
  if (nonWorkingWeekdays.length > 0) {
    timeGroups['weekdays-off'] = nonWorkingWeekdays;
  }

  // Добавляем нерабочие выходные как отдельную группу
  if (nonWorkingWeekends.length > 0) {
    timeGroups['weekend-off'] = nonWorkingWeekends;
  }

  // Преобразуем в результат
  const result: ScheduleGroup[] = [];

  Object.entries(timeGroups).forEach(([groupKey, days]) => {
    // Сортируем дни по порядку недели
    const sortedDays = days.sort((a, b) => dayOrder.indexOf(a) - dayOrder.indexOf(b));

    let daysString: string;
    let timeString: string;

    if (groupKey === 'weekdays-off' || groupKey === 'weekend-off') {
      // Обрабатываем нерабочие дни
      daysString = formatDaysRange(sortedDays);
      timeString = 'выходной';
    } else {
      // Обрабатываем рабочие дни
      daysString = formatDaysRange(sortedDays);
      const timeKey = groupKey.replace(/^(weekdays|weekend)-/, '');
      const [startTime, endTime] = timeKey.split('-');
      timeString = `${startTime} - ${endTime}`;
    }

    result.push({
      days: daysString,
      time: timeString,
    });
  });

  // Сортируем результат: сначала будни, потом выходные
  return result.sort((a, b) => {
    const aIsWeekend = a.time === 'выходной' && (a.days.includes('сб') || a.days.includes('вс'));
    const bIsWeekend = b.time === 'выходной' && (b.days.includes('сб') || b.days.includes('вс'));
    const aIsWorkingWeekend = a.time !== 'выходной' && (a.days.includes('сб') || a.days.includes('вс'));
    const bIsWorkingWeekend = b.time !== 'выходной' && (b.days.includes('сб') || b.days.includes('вс'));

    // Рабочие будни идут первыми
    if (!aIsWeekend && !aIsWorkingWeekend && (bIsWeekend || bIsWorkingWeekend)) return -1;
    if ((aIsWeekend || aIsWorkingWeekend) && !bIsWeekend && !bIsWorkingWeekend) return 1;

    // Рабочие выходные идут после рабочих будней, но перед нерабочими днями
    if (aIsWorkingWeekend && bIsWeekend) return -1;
    if (aIsWeekend && bIsWorkingWeekend) return 1;

    // Если оба дня в одной категории, сортируем по первому дню недели в группе
    if (
      (aIsWeekend && bIsWeekend) ||
      (aIsWorkingWeekend && bIsWorkingWeekend) ||
      (!aIsWeekend && !aIsWorkingWeekend && !bIsWeekend && !bIsWorkingWeekend)
    ) {
      const aFirstDay = getFirstDayFromGroup(a.days);
      const bFirstDay = getFirstDayFromGroup(b.days);
      return dayOrder.indexOf(aFirstDay) - dayOrder.indexOf(bFirstDay);
    }

    return 0;
  });
});

// Функция для форматирования диапазона дней
const formatDaysRange = (days: string[]): string => {
  if (days.length === 0) return '';
  if (days.length === 1) return dayNamesMap[days[0]];

  // Проверяем, являются ли дни последовательными
  const dayIndices = days.map((day) => dayOrder.indexOf(day));
  const isConsecutive = dayIndices.every((index, i) => {
    if (i === 0) return true;
    return index === dayIndices[i - 1] + 1;
  });

  if (isConsecutive && days.length >= 2) {
    // Если дни идут подряд (2 и более), показываем диапазон
    return `${dayNamesMap[days[0]]}-${dayNamesMap[days[days.length - 1]]}`;
  } else {
    // Иначе перечисляем через запятую
    return days.map((day) => dayNamesMap[day]).join(', ');
  }
};
</script>

<style scoped>
.working-hours {
  display: flex;
  flex-direction: column;
  gap: 2px;
  cursor: pointer;
}

.working-hours__item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 14px;
  line-height: 1.4;
}

.working-hours__days {
  font-weight: 500;
}

.working-hours__time {
  font-weight: 400;
  margin-left: 8px;
}

/* Стили для модального окна */
:deep(.working-hours-dialog .p-dialog) {
  width: auto;
  min-width: 300px;
  max-width: 400px;
}

:deep(.working-hours-dialog .p-dialog-header) {
  padding: 24px 24px 16px 24px;
  text-align: center;
}

:deep(.working-hours-dialog .p-dialog-title) {
  font-size: 20px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0;
}

:deep(.working-hours-dialog .p-dialog-content) {
  padding: 0 24px 24px 24px;
}

.working-hours-dialog__content {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.working-hours-dialog__item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 16px;
  line-height: 1.4;
  padding: 2px 0;
}

.working-hours-dialog__day {
  font-weight: 400;
  color: #1a1a1a;
  min-width: 24px;
}

.working-hours-dialog__time {
  font-weight: 400;
  color: #1a1a1a;
  margin-left: 16px;
}
</style>
