import { Calendar } from '@fullcalendar/core'
import interactionPlugin from '@fullcalendar/interaction'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import multiMonthPlugin from '@fullcalendar/multimonth'
import scrollGridPlugin from '@fullcalendar/scrollgrid'
import timelinePlugin from '@fullcalendar/timeline'
import adaptivePlugin from '@fullcalendar/adaptive'
import resourcePlugin from '@fullcalendar/resource'
import resourceDayGridPlugin from '@fullcalendar/resource-daygrid'
import resourceTimelinePlugin from '@fullcalendar/resource-timeline'
import resourceTimegridPlugin from '@fullcalendar/resource-timegrid'
import rrulePlugin from '@fullcalendar/rrule'
import momentPlugin from '@fullcalendar/moment'
import momentTimezonePlugin from '@fullcalendar/moment-timezone'
import locales from '@fullcalendar/core/locales-all'
import { elementClosest } from '@fullcalendar/core/internal'

export default function fullcalendar({
    locale,
    plugins,
    schedulerLicenseKey,
    timeZone,
    config,
    editable,
    selectable,
}) {
    return {
        init() {
            let moveEvent = false;

            /** @type Calendar */
            const calendar = new Calendar(this.$el, {
                headerToolbar: {
                    'left': 'prev,next today',
                    'center': 'title',
                    'right': 'dayGridMonth,dayGridWeek,dayGridDay',
                },
                plugins: plugins.map(plugin => availablePlugins[plugin]),
                locale,
                schedulerLicenseKey,
                timeZone,
                editable,
                selectable,
                ...config,
                locales,
                events: (info, successCallback, failureCallback) => {
                    this.$wire.fetchEvents({ start: info.startStr, end: info.endStr, timezone: info.timeZone })
                        .then(successCallback)
                        .catch(failureCallback)
                },
                eventDidMount: (jsEvent) => {
                    if(moveEvent && jsEvent.event.display == 'inverse-background') {
                        jsEvent.el.style.backgroundColor = 'green';
                    }
                },
                eventClick: ({ event, jsEvent }) => {
                    //console.log('eventClick');

                    jsEvent.preventDefault()

                    /*if (event.url) {
                        const isNotPlainLeftClick = e => (e.which > 1) || (e.ctrlKey) || (e.metaKey) || (e.shiftKey)
                        window.open(event.url, '_blank')
                    }*/
                    if(jsEvent.altKey) {
                        return this.$wire.onEventClick(event, 'alt');
                    } else if(jsEvent.ctrlKey) {
                        if (event.url) {
                            return window.open(event.url, '_blank');
                        }
                        return this.$wire.onEventClick(event, 'ctrl');
                    } else if(jsEvent.shiftKey) {
                        if (event.url) {
                           return window.open(event.url, '_blank');
                        }
                        return this.$wire.onEventClick(event, 'shift');
                    }

                    this.$wire.onEventClick(event)
                },
                eventDrop: async ({ event, oldEvent, oldResource, newResource, relatedEvents, delta, revert }) => {
                    //console.log('eventDrop');

                    const shouldRevert = await this.$wire.onEventDrop(event, oldEvent, relatedEvents, delta, oldResource, newResource)

                    if (typeof shouldRevert === 'boolean' && shouldRevert) {
                        revert()
                    }
                },
                eventResize: async ({ event, oldEvent, relatedEvents, startDelta, endDelta, revert }) => {
                    //console.log('eventResize');

                    const shouldRevert = await this.$wire.onEventResize(event, oldEvent, relatedEvents, startDelta, endDelta)

                    if (typeof shouldRevert === 'boolean' && shouldRevert) {
                        revert()
                    }
                },
                resources: (info, successCallback, failureCallback) => {
                    this.$wire.fetchResources({ start: info.startStr, end: info.endStr, timezone: info.timeZone })
                        .then(successCallback)
                        .catch(failureCallback);
                },
                resourceLabelContent: (args) => {
                    if(args.resource.extendedProps.url) {
                        return {html:`<a href="${args.resource.extendedProps.url}">${args.resource.title}</a>`};
                    }

                    return args.resource.title;
                },
                dateClick: ({ dateStr, allDay, view, resource }) => {
                    //console.log('dateClick');
                    if (!selectable) return;
                    this.$wire.onDateClick(dateStr, null, allDay, view, resource)
                },
                select: ({ startStr, endStr, allDay, view, resource }) => {
                    //console.log('select');
                    if (!selectable) return;
                    this.$wire.onSlotSelect(startStr, endStr, allDay, view, resource, moveEvent)
                },
                selectOverlap: function (event) {
                    return event.groupId !== "blockedRooms";
                },
            })

            calendar.render()

            window.addEventListener('filament-fullcalendar--refresh', () => calendar.refetchEvents())

            window.addEventListener('filament-fullcalendar--reload', () => {
                calendar.refetchResources();
                calendar.refetchEvents();
            })

            window.addEventListener('filament-fullcalendar--enable-move', () => {
                moveEvent = true;
                let elements = document.getElementsByClassName('fc-bg-event');

                for(var i = 0; i < elements.length; i++) {
                    elements[i].style.backgroundColor = 'green'
                }
            })

            window.addEventListener('filament-fullcalendar--disable-move', () => {
                moveEvent = false;
                let elements = document.getElementsByClassName('fc-bg-event');

                for(var i = 0; i < elements.length; i++) {
                    elements[i].style.backgroundColor = null;
                }
            })
        },
    }
}

const availablePlugins = {
    'interaction': interactionPlugin,
    'dayGrid': dayGridPlugin,
    'timeGrid': timeGridPlugin,
    'list': listPlugin,
    'multiMonth': multiMonthPlugin,
    'scrollGrid': scrollGridPlugin,
    'timeline': timelinePlugin,
    'adaptive': adaptivePlugin,
    'resource': resourcePlugin,
    'resourceDayGrid': resourceDayGridPlugin,
    'resourceTimeline': resourceTimelinePlugin,
    'resourceTimeGrid': resourceTimegridPlugin,
    'rrule': rrulePlugin,
    'moment': momentPlugin,
    'momentTimezone': momentTimezonePlugin,
}
