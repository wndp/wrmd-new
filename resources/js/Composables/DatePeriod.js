import {
    format, parseISO, differenceInDays,
    startOfWeek, startOfMonth, startOfYear,
    endOfWeek, endOfMonth, endOfYear,
    subDays, subWeeks, subMonths, subYears,
    getDay, getDate, getDayOfYear,
    setDate, setDay, setDayOfYear,
} from 'date-fns';

export const datePeriods = [
    {
        value: 'today',
        label: 'Today',
    },
    {
        value: 'past-7-days',
        label: 'Past 7 days',
    },
    {
        value: 'this-week',
        label: 'This week',
    },
    {
        value: 'this-month',
        label: 'This month',
    },
    {
        value: 'this-year',
        label: 'This year',
    },
    {
        value: 'yesterday',
        label: 'Yesterday',
    },
    {
        value: 'last-week',
        label: 'Last week',
    },
    {
        value: 'last-week-to-date',
        label: 'Last week-to-date',
    },
    {
        value: 'last-month',
        label: 'Last month',
    },
    {
        value: 'last-month-to-date',
        label: 'Last month-to-date',
    },
    {
        value: 'last-year',
        label: 'Last year',
    },
    {
        value: 'last-year-to-date',
        label: 'Last year-to-date',
    },
    {
        value: 'all-dates',
        label: 'All dates',
    },
    {
        value: 'custom',
        label: 'Custom'
    }
];

export function dateFromPeriod(period) {
    let now = new Date(),
        from = '',
        to = now;

    if (period === 'custom') {
        return;
    }

    switch (period) {
        case 'today':
            from = now;
            break;

        case 'past-7-days':
            from = subDays(now, 6);
            break;

        case 'this-week':
            from = startOfWeek(now);
            break;

        case 'this-month':
            from = startOfMonth(now);
            break;

        case 'this-year':
            from = startOfYear(now);
            break;

        case 'yesterday':
            from = subDays(now, 1);
            to = subDays(now, 1);
            break;

        case 'last-week':
            from = startOfWeek(subWeeks(now, 1));
            to = endOfWeek(subWeeks(now, 1));
            break;

        case 'last-week-to-date':
            from = startOfWeek(subWeeks(now, 1));
            to = setDay(subWeeks(now, 1), getDay(now));
            break;

        case 'last-month':
            from = startOfMonth(subMonths(now, 1));
            to = endOfMonth(subMonths(now, 1));
            break;

        case 'last-month-to-date':
            from = startOfMonth(subMonths(now, 1));
            to = setDate(subMonths(now, 1), getDate(now));
            break;

        case 'last-year':
            from = startOfYear(subYears(now, 1));
            to = endOfYear(subYears(now, 1));
            break;

        case 'last-year-to-date':
            from = startOfYear(subYears(now, 1));
            to = setDayOfYear(subYears(now, 1), getDayOfYear(now));
            break;
    }

    return {
        from: format(from, 'yyyy-MM-dd'),
        to: format(to, 'yyyy-MM-dd')
    };
}

export function compareDateFromPeriod(period, comparePeriod) {
    let now = new Date(),
        from = '',
        to = '';

    if (period === 'custom') {
        return;
    }

    switch (period) {
        case 'today':
            if (comparePeriod === 'previousperiod') {
                from = subDays(now, 1);
                to = subDays(now, 1);
            } else if (comparePeriod === 'previousyear') {
                from = subYears(now, 1);
                to = subYears(now, 1);
            }
            break;

        case 'past-7-days':
            if (comparePeriod === 'previousperiod') {
                from = subDays(now, 13);
                to = subDays(now, 7);
            } else if (comparePeriod === 'previousyear') {
                from = subYears(subDays(now, 6), 1);
                to = subYears(now, 1);
            }
            break;

        case 'this-week':
            if (comparePeriod === 'previousperiod') {
                from = startOfWeek(subWeeks(now, 1));
                to = endOfWeek(subWeeks(now, 1));
            } else if (comparePeriod === 'previousyear') {
                from = startOfWeek(subYears(now, 1));
                to = subYears(now, 1);
            }
            break;

        case 'this-month':
            if (comparePeriod === 'previousperiod') {
                from = startOfMonth(subMonths(now, 1));
                to = endOfMonth(subMonths(now, 1));
            } else if (comparePeriod === 'previousyear') {
                from = startOfMonth(subYears(now, 1));
                to = subYears(now, 1);
            }
            break;

        case 'this-year':
            if (comparePeriod === 'previousperiod') {
                from = startOfYear(subYears(now, 1));
                to = endOfYear(subYears(now, 1));
            } else if (comparePeriod === 'previousyear') {
                from = startOfYear(subYears(now, 1));
                to = subYears(now, 1);
            }
            break;

        case 'yesterday':
            if (comparePeriod === 'previousperiod') {
                from = subDays(now, 2);
                to = subDays(now, 2);
            } else if (comparePeriod === 'previousyear') {
                from = subYears(subDays(now, 1), 1);
                to = subYears(subDays(now, 1), 1);
            }
            break;

        case 'last-week':
            if (comparePeriod === 'previousperiod') {
                from = startOfWeek(subWeeks(now, 2));
                to = endOfWeek(subWeeks(now, 2));
            } else if (comparePeriod === 'previousyear') {
                from = startOfWeek(subWeeks(subYears(now, 1), 1));
                to = endOfWeek(subWeeks(subYears(now, 1), 1));
            }
            break;

        case 'last-week-to-date':
            if (comparePeriod === 'previousperiod') {
                from = startOfWeek(subWeeks(now, 2));
                to = setDay(subWeeks(now, 2), getDay(now));
            } else if (comparePeriod === 'previousyear') {
                from = startOfWeek(subWeeks(subYears(now, 1), 1));
                to = setDay(subWeeks(subYears(now, 1), 1), getDay(now));
            }
            break;

        case 'last-month':
            if (comparePeriod === 'previousperiod') {
                from = startOfMonth(subMonths(now, 2));
                to = endOfMonth(subMonths(now, 2));
            } else if (comparePeriod === 'previousyear') {
                from = startOfMonth(subMonths(subYears(now, 1), 1));
                to = endOfMonth(subMonths(subYears(now, 1), 1));
            }
            break;

        case 'last-month-to-date':
            if (comparePeriod === 'previousperiod') {
                from = startOfMonth(subMonths(now, 2));
                to = setDate(subMonths(now, 2), getDate(now));
            } else if (comparePeriod === 'previousyear') {
                from = startOfMonth(subMonths(subYears(now, 1), 1));
                to = setDate(subMonths(subYears(now, 1), 1), getDate(now));
            }
            break;

        case 'last-year':
            if (comparePeriod === 'previousperiod') {
                from = startOfYear(subYears(now, 2));
                to = endOfYear(subYears(now, 2));
            } else if (comparePeriod === 'previousyear') {
                from = startOfYear(subYears(now, 2));
                to = endOfYear(subYears(now, 2));
            }
            break;

        case 'last-year-to-date':
            if (comparePeriod === 'previousperiod') {
                from = startOfYear(subYears(now, 2));
                to = setDate(subYears(now, 2), getDate(now));
            } else if (comparePeriod === 'previousyear') {
                from = startOfYear(subYears(now, 2));
                to = setDate(subYears(now, 2), getDate(now));
            }
            break;
    }

    return {
        from: format(from, 'yyyy-MM-dd'),
        to: format(to, 'yyyy-MM-dd')
    };
}

export function customDatesFromPeriod(from, to, comparePeriod) {
    let fromCompare, toCompare;

    from = parseISO(from);
    to = parseISO(to);

    if (comparePeriod === 'previousperiod') {
        let diffDays = differenceInDays(to, from) + 1; //moment(to).diff(from, 'days') + 1;

        fromCompare = subDays(from, diffDays); //moment(from).subtract(diffDays, 'days');
        toCompare = subDays(to, diffDays); //moment(to).subtract(diffDays, 'days');
    } else if (comparePeriod === 'previousyear') {
        fromCompare = subYears(from, 1); // moment(from).subtract(1,'years');
        toCompare = subYears(to, 1); // moment(to).subtract(1,'years');
    }

    return {
        from: format(fromCompare, 'yyyy-MM-dd'),
        to: format(toCompare, 'yyyy-MM-dd')
    };
}
