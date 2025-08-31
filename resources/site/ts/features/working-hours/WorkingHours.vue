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

const isDialogVisible = ref(false);

const openDialog = () => {
  isDialogVisible.value = true;
};

const dayNamesMap: Record<string, string> = {
  monday: 'пн',
  tuesday: 'вт',
  wednesday: 'ср',
  thursday: 'чт',
  friday: 'пт',
  saturday: 'сб',
  sunday: 'вс',
};

const dayOrder = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

const reverseNameMap: Record<string, string> = {
  пн: 'monday',
  вт: 'tuesday',
  ср: 'wednesday',
  чт: 'thursday',
  пт: 'friday',
  сб: 'saturday',
  вс: 'sunday',
};

const getFirstDayFromGroup = (daysString: string): string => {
  if (daysString.includes('-')) {
    const firstDayRu = daysString.split('-')[0];
    return reverseNameMap[firstDayRu] || 'monday';
  }

  if (daysString.includes(',')) {
    const firstDayRu = daysString.split(',')[0].trim();
    return reverseNameMap[firstDayRu] || 'monday';
  }

  return reverseNameMap[daysString] || 'monday';
};

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

  const weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
  const weekendDays = ['saturday', 'sunday'];

  const timeGroups: Record<string, string[]> = {};

  Object.entries(props.workingHours).forEach(([day, schedule]) => {
    const timeKey = `${schedule.startTime}-${schedule.endTime}`;
    let groupKey: string;

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

  const workingDays = Object.keys(props.workingHours);
  const nonWorkingWeekdays = weekdays.filter((day) => !workingDays.includes(day));
  const nonWorkingWeekends = weekendDays.filter((day) => !workingDays.includes(day));

  if (nonWorkingWeekdays.length > 0) {
    timeGroups['weekdays-off'] = nonWorkingWeekdays;
  }

  if (nonWorkingWeekends.length > 0) {
    timeGroups['weekend-off'] = nonWorkingWeekends;
  }

  const result: ScheduleGroup[] = [];

  Object.entries(timeGroups).forEach(([groupKey, days]) => {
    const sortedDays = days.sort((a, b) => dayOrder.indexOf(a) - dayOrder.indexOf(b));

    let daysString: string;
    let timeString: string;

    if (groupKey === 'weekdays-off' || groupKey === 'weekend-off') {
      daysString = formatDaysRange(sortedDays);
      timeString = 'выходной';
    } else {
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

  return result.sort((a, b) => {
    const aIsWeekend = a.time === 'выходной' && (a.days.includes('сб') || a.days.includes('вс'));
    const bIsWeekend = b.time === 'выходной' && (b.days.includes('сб') || b.days.includes('вс'));
    const aIsWorkingWeekend = a.time !== 'выходной' && (a.days.includes('сб') || a.days.includes('вс'));
    const bIsWorkingWeekend = b.time !== 'выходной' && (b.days.includes('сб') || b.days.includes('вс'));

    if (!aIsWeekend && !aIsWorkingWeekend && (bIsWeekend || bIsWorkingWeekend)) return -1;
    if ((aIsWeekend || aIsWorkingWeekend) && !bIsWeekend && !bIsWorkingWeekend) return 1;

    if (aIsWorkingWeekend && bIsWeekend) return -1;
    if (aIsWeekend && bIsWorkingWeekend) return 1;

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

const formatDaysRange = (days: string[]): string => {
  if (days.length === 0) return '';
  if (days.length === 1) return dayNamesMap[days[0]];

  const dayIndices = days.map((day) => dayOrder.indexOf(day));
  const isConsecutive = dayIndices.every((index, i) => {
    if (i === 0) return true;
    return index === dayIndices[i - 1] + 1;
  });

  if (isConsecutive && days.length >= 2) {
    return `${dayNamesMap[days[0]]}-${dayNamesMap[days[days.length - 1]]}`;
  } else {
    return days.map((day) => dayNamesMap[day]).join(', ');
  }
};
</script>
