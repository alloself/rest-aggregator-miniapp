<template>
  <div class="working-hours">
    <div v-for="group in groupedSchedule" :key="group.days" class="working-hours__item">
      <span class="working-hours__days">{{ `${group.days}:` }}</span>
      <span class="working-hours__time">{{ group.time }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

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

const groupedSchedule = computed((): ScheduleGroup[] => {
  if (!props.workingHours) return [];

  // Группируем дни по времени работы
  const timeGroups: Record<string, string[]> = {};

  // Сначала добавляем рабочие дни
  Object.entries(props.workingHours).forEach(([day, schedule]) => {
    const timeKey = `${schedule.startTime}-${schedule.endTime}`;
    if (!timeGroups[timeKey]) {
      timeGroups[timeKey] = [];
    }
    timeGroups[timeKey].push(day);
  });

  // Находим выходные дни
  const workingDays = Object.keys(props.workingHours);
  const weekendDays = dayOrder.filter((day) => !workingDays.includes(day));

  // Добавляем выходные как отдельную группу
  if (weekendDays.length > 0) {
    timeGroups['weekend'] = weekendDays;
  }

  // Преобразуем в результат
  const result: ScheduleGroup[] = [];

  Object.entries(timeGroups).forEach(([timeKey, days]) => {
    // Сортируем дни по порядку недели
    const sortedDays = days.sort((a, b) => dayOrder.indexOf(a) - dayOrder.indexOf(b));

    let daysString: string;
    let timeString: string;

    if (timeKey === 'weekend') {
      // Обрабатываем выходные
      daysString = formatDaysRange(sortedDays);
      timeString = 'выходной';
    } else {
      // Обрабатываем рабочие дни
      daysString = formatDaysRange(sortedDays);
      const [startTime, endTime] = timeKey.split('-');
      timeString = `${startTime} - ${endTime}`;
    }

    result.push({
      days: daysString,
      time: timeString,
    });
  });

  // Сортируем результат: сначала рабочие дни в порядке недели, потом выходные
  return result.sort((a, b) => {
    if (a.time === 'выходной' && b.time !== 'выходной') return 1;
    if (a.time !== 'выходной' && b.time === 'выходной') return -1;

    // Если обе группы рабочие дни, сортируем по первому дню недели в группе
    if (a.time !== 'выходной' && b.time !== 'выходной') {
      // Найдем первый день в каждой группе
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

  if (isConsecutive && days.length > 2) {
    // Если дни идут подряд, показываем диапазон
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
</style>
