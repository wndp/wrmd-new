import { parseISO, intlFormat, format } from 'date-fns';
import { toZonedTime } from 'date-fns-tz';

export function dateForHumans(date) {
    return intlFormat(toZonedTime(parseISO(date), null));
}

export function currentDateForHumans() {
    return format(new Date(), "EEEE, MMM do yyyy 'at' h:mbbb");
}

export function dateInIso8601(date) {
    return format(toZonedTime(parseISO(date), null), 'yyyy-MM-dd');
}
