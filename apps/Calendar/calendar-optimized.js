/**
 * calendar-optimized.js - Optimized JavaScript for Calendar functionality
 * This script improves performance by:
 * 1. Using selective DOM updates instead of full reloads
 * 2. Implementing efficient event handling
 * 3. Optimizing AJAX calls with data caching
 * 4. Debouncing and throttling expensive operations
 */
console.log('Calendar Optimized script loaded');


document.addEventListener("DOMContentLoaded", function() {
    console.log('DOM loaded - initializing calendar optimizations');

    // Calendar state management
    window.CalendarApp = {
        events: {}, // Cache for events data 
        lastFetchTime: null,
        isLoading: false,
        dragState: {},
        eventFilters: {
            PhoneNote: true,
            CalendarEvent: true,
            ShipmentArrived: true,
            ShipmentETA: true
        },
        currentPopup: null
    };
    
    // Initialize the application
    function initCalendar() {
        // Initialize filters from localStorage if available
        initializeFilters();
        
        // Set up event handlers
        setupEventHandlers();
        
        // Scrolls to current date
        scrollToCurrentDate();
        
        // Fetch events for the first time
        fetchCalendarEvents();
        
        // Set up periodic updates (with optimization compared to current code)
        setupPeriodicUpdates();
    }
    
    // Initialize filters from localStorage
    function initializeFilters() {
        const savedFilters = localStorage.getItem('calendarFilters');
        if (savedFilters) {
            try {
                const filters = JSON.parse(savedFilters);
                CalendarApp.eventFilters = { ...CalendarApp.eventFilters, ...filters };
                
                // Apply saved states to checkboxes
                document.getElementById('filter-phone-note').checked = CalendarApp.eventFilters.PhoneNote;
                document.getElementById('filter-calendar-event').checked = CalendarApp.eventFilters.CalendarEvent;
                document.getElementById('filter-shipment-arrived').checked = CalendarApp.eventFilters.ShipmentArrived;
                document.getElementById('filter-shipment-eta').checked = CalendarApp.eventFilters.ShipmentETA;
                
                // Apply filters immediately 
                updateEventVisibility();
            } catch (e) {
                console.error('Error loading saved filters:', e);
            }
        }
    }
    
    // Set up all event handlers
    function setupEventHandlers() {
        // Off-canvas menu handlers
        setupMenuHandlers();
        
        // Filter change handlers
        setupFilterHandlers();
        
        // Draggable/droppable handlers
        setupDragDropHandlers();
        
        // Right-click context menu handlers
        setupContextMenuHandlers();
        
        // Popup handlers
        setupPopupHandlers();
        
        // Global click handler to hide context menus
        document.addEventListener('click', hideAllContextMenus);
        
        // Escape key handler
        document.addEventListener('keyup', function(e) {
            if (e.key === "Escape") {
                closeAllPopups();
            }
        });
    }
    
    // Setup menu toggle handlers
    function setupMenuHandlers() {
        const offCanvasMenu = document.getElementById("off-canvas-menu");
        const openMenuButton = document.getElementById("open-menu");
        const closeMenuButton = document.getElementById("close-menu");
        
        if (openMenuButton) {
            openMenuButton.addEventListener("click", function() {
                offCanvasMenu.classList.remove("-translate-x-full");
            });
        }
        
        if (closeMenuButton) {
            closeMenuButton.addEventListener("click", function() {
                offCanvasMenu.classList.add("-translate-x-full");
            });
        }
    }
    
    // Setup filter change handlers with debounce
    function setupFilterHandlers() {
        const phoneNoteCheckbox = document.getElementById("filter-phone-note");
        const calendarEventCheckbox = document.getElementById("filter-calendar-event");
        const shipmentArrivedCheckbox = document.getElementById("filter-shipment-arrived");
        const shipmentETACheckbox = document.getElementById("filter-shipment-eta");
        
        // Using the more efficient way to attach event listeners
        [
            { elem: phoneNoteCheckbox, type: 'PhoneNote' },
            { elem: calendarEventCheckbox, type: 'CalendarEvent' },
            { elem: shipmentArrivedCheckbox, type: 'ShipmentArrived' },
            { elem: shipmentETACheckbox, type: 'ShipmentETA' }
        ].forEach(item => {
            if (item.elem) {
                item.elem.addEventListener("change", function() {
                    CalendarApp.eventFilters[item.type] = this.checked;
                    
                    // Save filters to localStorage
                    localStorage.setItem('calendarFilters', JSON.stringify(CalendarApp.eventFilters));
                    
                    // Update visibility without full page reload
                    updateEventVisibility();
                });
            }
        });
    }
    
    // Update event visibility based on filters
    function updateEventVisibility() {
        console.log('Updating event visibility');

        document.querySelectorAll(".event").forEach(event => {
            const eventType = event.getAttribute("EventType");
            if (eventType && CalendarApp.eventFilters.hasOwnProperty(eventType)) {
                event.style.display = CalendarApp.eventFilters[eventType] ? "block" : "none";
            }
        });
    }
    
    // Setup drag-drop handlers
    function setupDragDropHandlers() {
        const draggableElements = document.querySelectorAll('.draggable');
        const droppableElements = document.querySelectorAll('.droppable');
        
        // Use jQuery for draggable/droppable as it's already loaded
        // But optimize the implementation
        $(draggableElements).draggable({
            start: function(e) {
                const id = e.target.id.split("_")[1];
                CalendarApp.dragState = {
                    drag: id,
                    EventID: $(this).attr('EventID'),
                    EventType: $(this).attr('EventType'),
                    ParentABlock: $(this).siblings().attr('CurrentDateBlock')
                };
            },
            appendTo: 'body',
            containment: "window",
            scroll: false,
            helper: 'clone',
            // Add revert to improve UX when drag fails
            revert: 'invalid'
        });
        
        $(droppableElements).droppable({
            drop: function(event, ui) {
                const $this = $(this);
                const cellId = event.target.id;
                const targetId = cellId.split("_")[1];
                
                if (CalendarApp.dragState.drag === targetId) {
                    let NewParentABlock = cellId.substring(3);
                    
                    const confirmMove = confirm(`Are you sure you want to move ${CalendarApp.dragState.EventType} ID# ${CalendarApp.dragState.EventID} from ${CalendarApp.dragState.ParentABlock} to ${NewParentABlock}?`);
                    
                    if (confirmMove) {
                        // Optimize by using Promise for the AJAX call
                        moveEvent(NewParentABlock, CalendarApp.dragState.EventType, CalendarApp.dragState.EventID)
                            .then(() => {
                                // Success - update the visual state
                                $this.append(ui.draggable);
                                
                                // Position correctly
                                const width = $this.width();
                                const height = $this.height();
                                const cntrLeft = (width / 2) - (ui.draggable.width() / 2);
                                const cntrTop = (height / 2) - (ui.draggable.height() / 2);
                                
                                ui.draggable.css({
                                    left: cntrLeft + "px",
                                    top: cntrTop + "px"
                                });
                                
                                // Update local cache
                                updateEventDateInCache(CalendarApp.dragState.EventID, NewParentABlock);
                            })
                            .catch(error => {
                                console.error("Error moving event:", error);
                                // Return to original position on error
                                ui.draggable.animate({ top: 0, left: 0 }, 300);
                            });
                    } else {
                        ui.helper.hide();
                        $(".droppable").draggable("option", "cancel", ".title");
                    }
                }
            },
            // Highlight drop target for better UX
            hoverClass: "drop-hover",
            accept: '.draggable'
        });
    }
    
    // AJAX helper function for moving events
    function moveEvent(newDate, eventType, eventId) {
        return new Promise((resolve, reject) => {
            $.get("ajax.php", {
                NewDate: newDate,
                EventType: eventType,
                EventID: eventId
            })
            .done(function(data) {
                resolve(data);
            })
            .fail(function(error) {
                reject(error);
            });
        });
    }
    
    // Update the event date in our local cache
    function updateEventDateInCache(eventId, newDate) {
        // Find the event in our cache and update its date
        const dateParts = newDate.split('-');
        if (dateParts.length === 3) {
            const year = dateParts[0];
            const month = dateParts[1];
            const day = dateParts[2];
            
            // This is a simplified example - your actual cache structure may differ
            if (CalendarApp.events[eventId]) {
                CalendarApp.events[eventId].year = year;
                CalendarApp.events[eventId].month = month;
                CalendarApp.events[eventId].day = day;
            }
        }
    }
    
    // Setup right-click context menu handlers with improved event delegation
    function setupContextMenuHandlers() {
        // Event context menu
        document.body.addEventListener('contextmenu', function(event) {
            const target = event.target.closest('.CustomRightClick');
            if (target) {
                event.preventDefault();
                
                const eventId = target.dataset.id;
                if (!eventId) return;
                
                // Hide all other menus
                hideAllContextMenus();
                
                // Show this menu
                const menu = document.getElementById("rmenu");
                menu.className = "show";
                menu.style.top = (event.clientY - 50) + 'px';
                menu.style.left = event.clientX + 'px';
                
                // Setup command links
                document.getElementById("ButtonOpenEvent").href = `https://vgrcanada.org/apps/Calendar/calendarevent.php?calendareventid=${eventId}`;
                document.getElementById("ButtonEditEvent").href = `https://vgrcanada.org/apps/Calendar/calendareventedit.php?calendareventid=${eventId}&action=edit`;
                document.getElementById("ButtonDeleteEvent").href = `https://vgrcanada.org/apps/Calendar/calendarevent.php?actionform=delete&calendareventid=${eventId}`;
                document.getElementById("ButtonDuplicateEvent").href = `https://vgrcanada.org/apps/Calendar/calendareventedit.php?actionform=duplicate&calendareventid=${eventId}`;
                
                // Setup copy handler
                document.getElementById("ButtonCopyEvent").onclick = function() {
                    copyEvent(eventId);
                };
            }
            
            // Day cell context menu
            const cellTarget = event.target.closest('.CustomRightClickCase');
            if (cellTarget) {
                event.preventDefault();
                
                const dateId = cellTarget.dataset.id;
                if (!dateId) return;
                
                // Hide all other menus
                hideAllContextMenus();
                
                // Show this menu
                const menu = document.getElementById("rmenuCase");
                menu.className = "show";
                menu.style.top = (event.clientY - 50) + 'px';
                menu.style.left = event.clientX + 'px';
                
                // Setup command links
                document.getElementById("ButtonNewEvent").href = `https://vgrcanada.org/apps/Calendar/calendareventedit.php?evdate=${dateId}`;
                document.getElementById("ButtonNewPhoneNote").href = `https://vgrcanada.org/apps/PhoneNotes/phonenoteedit.php?CalendarDate=${dateId}`;
                
                // Setup paste handler
                document.getElementById("ButtonPasteEvent").onclick = function() {
                    pasteEvent(dateId);
                };
            }
            
            // Phone note context menu
            const phoneNoteTarget = event.target.closest('.CustomRightClickPhoneNote');
            if (phoneNoteTarget) {
                event.preventDefault();
                
                const phoneNoteId = phoneNoteTarget.dataset.id;
                if (!phoneNoteId) return;
                
                // Hide all other menus
                hideAllContextMenus();
                
                // Show this menu
                const menu = document.getElementById("rmenuPhoneNote");
                menu.className = "show";
                menu.style.top = (event.clientY - 50) + 'px';
                menu.style.left = event.clientX + 'px';
                
                // Setup command links
                document.getElementById("ButtonOpenPhoneNote").href = `https://vgrcanada.org/apps/PhoneNotes/phonenotes.php?phonenoteid=${phoneNoteId}`;
                document.getElementById("ButtonEditPhoneNote").href = `https://vgrcanada.org/apps/PhoneNotes/phonenoteedit.php?action=edit&phonenoteid=${phoneNoteId}`;
            }
        });
    }
    
    // Hide all context menus
    function hideAllContextMenus() {
        document.getElementById("rmenu").className = "hide";
        document.getElementById("rmenuCase").className = "hide";
        document.getElementById("rmenuPhoneNote").className = "hide";
    }
    
    // Copy event handler
    function copyEvent(eventId) {
        return new Promise((resolve, reject) => {
            $.get("ajax.php", { EventIDtoCopy: eventId })
                .done(function(data) {
                    // Update UI to indicate successful copy if needed
                    // Enable paste button
                    const pasteBtn = document.getElementById("ButtonPasteEvent");
                    if (pasteBtn) {
                        pasteBtn.style.color = "black";
                    }
                    resolve(data);
                })
                .fail(function(error) {
                    reject(error);
                });
        });
    }
    
    // Paste event handler
    function pasteEvent(dateId) {
        return new Promise((resolve, reject) => {
            $.get("ajax.php", { PasteLocation: dateId })
                .done(function(data) {
                    // Refresh the calendar to show the new event
                    // But only update affected date cell to avoid full reload
                    refreshDateCell(dateId);
                    resolve(data);
                })
                .fail(function(error) {
                    reject(error);
                });
        });
    }
    
    // Refresh only a specific date cell
    function refreshDateCell(dateId) {
        // Get the current URL
        const currentPageUrl = window.location.href;
        
        // Find the cell to refresh
        const cellToRefresh = document.getElementById(`li-${dateId}`);
        if (!cellToRefresh) return;
        
        // Show loading indicator
        const loadingIndicator = document.createElement('div');
        loadingIndicator.className = 'cell-loading';
        loadingIndicator.innerHTML = '<div class="spinner"></div>';
        cellToRefresh.appendChild(loadingIndicator);
        
        // Load just this cell via AJAX
        $.get(`ajax.php`, { refreshCell: dateId })
            .done(function(data) {
                // Replace cell content
                if (data) {
                    // Keep the date number
                    const dateSpan = cellToRefresh.querySelector('span').cloneNode(true);
                    cellToRefresh.innerHTML = '';
                    cellToRefresh.appendChild(dateSpan);
                    
                    // Add new events
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data;
                    
                    // Extract events
                    const events = tempDiv.querySelectorAll('.event');
                    events.forEach(event => {
                        cellToRefresh.appendChild(event.cloneNode(true));
                    });
                    
                    // Re-initialize draggable events
                    setupDragDropHandlers();
                }
            })
            .fail(function() {
                // On failure, remove the loading indicator
                cellToRefresh.removeChild(loadingIndicator);
            });
    }
    
    // Setup popup handlers with improved popup management
    function setupPopupHandlers() {
        // Event delegation for all popup open clicks
        document.body.addEventListener('click', function(event) {
            // Check if the click was on a popup trigger
            const popupTrigger = event.target.closest('[onclick*="ShowExternalDataPopup"]');
            
            if (popupTrigger) {
                // Prevent default anchor behavior
                event.preventDefault();
                
                // Parse the onclick attribute to get parameters
                const onclickAttr = popupTrigger.getAttribute('onclick');
                if (!onclickAttr) return;
                
                const match = onclickAttr.match(/ShowExternalDataPopup\((\d+),\s*['"]([^'"]+)['"]\)/);
                if (match && match.length === 3) {
                    const eventId = match[1];
                    const appToDisplay = match[2];
                    
                    // Call the improved popup function
                    showPopup(eventId, appToDisplay);
                }
            }
            
            // Check if click was on a popup close button
            const closeButton = event.target.closest('.popup-close');
            if (closeButton) {
                const popup = closeButton.closest('.ExternalDataPopup');
                if (popup) {
                    closePopup(popup);
                }
            }
        });
    }
    
    // Improved popup display function
    function showPopup(eventId, appToDisplay) {
        const $popup = $('#ExternalDataPopup');
        
        // Set as current popup
        CalendarApp.currentPopup = $popup;
        
        // Show loading state immediately
        $popup.html(`
            <div class="popup-header">
                <span class="popup-title">Loading...</span>
                <button type="button" class="popup-close">✕</button>
            </div>
            <div class="popup-content" style="display:flex; justify-content:center; align-items:center; height:calc(100% - 40px);">
                <div style="text-align:center;">
                    <div class="loading-spinner" style="margin:0 auto 1rem; width:40px; height:40px;">
                        <svg viewBox="0 0 50 50" style="animation: rotate 2s linear infinite;">
                            <circle cx="25" cy="25" r="20" fill="none" stroke="#888" stroke-width="4" 
                                    style="stroke-dasharray: 100; stroke-dashoffset: 60;">
                            </circle>
                        </svg>
                    </div>
                    <div>Loading content...</div>
                </div>
            </div>
        `);
        
        // Initialize draggable/resizable
        try { $popup.draggable('destroy'); } catch(e){}
        try { $popup.resizable('destroy'); } catch(e){}
        
        $popup.draggable({
            handle: '.popup-header',
            start: function() {
                $(this).css({ transform: 'none' });
            },
            containment: 'window'
        }).resizable({
            handles: 'all',
            minWidth: 300,
            minHeight: 200
        });
        
        // Show popup
        $popup.removeClass('hide').show();
        
        // Check cache first
        const cacheKey = `${appToDisplay}-${eventId}`;
        if (CalendarApp.popupCache && CalendarApp.popupCache[cacheKey]) {
            updatePopupContent($popup, appToDisplay, CalendarApp.popupCache[cacheKey]);
            return;
        }
        
        // Load content with caching
        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: { [appToDisplay]: eventId },
            dataType: "html",
            success: function(data) {
                // Cache the response
                if (!CalendarApp.popupCache) CalendarApp.popupCache = {};
                CalendarApp.popupCache[cacheKey] = data;
                
                updatePopupContent($popup, appToDisplay, data);
            },
            error: function() {
                $popup.find('.popup-content').html('<div class="error">Error loading content. Please try again.</div>');
            }
        });
    }
    
    // Update popup content and reapply interactions
    function updatePopupContent($popup, appToDisplay, data) {
        const TextAppToDisplay = {
            'ShowPhoneNote': 'Phone Note',
            'CalendarEvent': 'Event',
            'Shipment': 'Shipment'
        }[appToDisplay] || 'Details';
        
        // Update content
        $popup.html(`
            <div class="popup-header">
                <span class="popup-title">${TextAppToDisplay}</span>
                <button type="button" class="popup-close">✕</button>
            </div>
            <div class="popup-content">${data}</div>
        `);
        
        // Reapply draggable and resizable
        try { $popup.draggable('destroy'); } catch(e){}
        try { $popup.resizable('destroy'); } catch(e){}
        
        $popup.draggable({
            handle: '.popup-header',
            start: function() {
                $(this).css({ transform: 'none' });
            },
            containment: 'window'
        }).resizable({
            handles: 'all',
            minWidth: 300,
            minHeight: 200
        });
    }
    
    // Close popup
    function closePopup(popup) {
        if (popup) {
            $(popup).addClass('hide').hide();
        }
    }
    
    // Close all popups
    function closeAllPopups() {
        $('.ExternalDataPopup').addClass('hide').hide();
        CalendarApp.currentPopup = null;
    }
    
    // Scroll to current date
    function scrollToCurrentDate() {
        const currentDate = new Date();
        const formattedDate = currentDate.toISOString().split('T')[0];
        const currentDateElement = document.getElementById(`li-${formattedDate}`);
        
        if (currentDateElement) {
            // Add a bit of delay to ensure the calendar is fully rendered
            setTimeout(() => {
                currentDateElement.scrollIntoView({
                    behavior: 'smooth', 
                    block: 'center'
                });
            }, 100);
        }
    }
    
    // Fetch calendar events with optimized approach
    function fetchCalendarEvents() {
        // Don't fetch if already loading
        if (CalendarApp.isLoading) return;
        
        CalendarApp.isLoading = true;
        
        // Store checkbox states before refresh
        const filterStates = { ...CalendarApp.eventFilters };
        
        // Add a light visual indicator for refresh
        const refreshIndicator = document.createElement('div');
        refreshIndicator.className = 'calendar-refreshing';
        refreshIndicator.innerHTML = '<div class="refresh-spinner"></div>';
        document.body.appendChild(refreshIndicator);
        
        // Only fetch changes since last update if possible
        const lastFetchParam = CalendarApp.lastFetchTime ? 
            `&lastUpdate=${encodeURIComponent(CalendarApp.lastFetchTime)}` : '';
        
        // Get the current page URL for the AJAX request
        const currentPageUrl = window.location.href;
        
        // Fetch only event data, not the entire calendar
        $.ajax({
            url: 'ajax.php',
            data: `getCalendarEvents=true${lastFetchParam}`,
            method: 'GET',
            success: function(response) {
                try {
                    // Parse the response
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (data.events) {
                        // Update our cache
                        CalendarApp.events = { ...CalendarApp.events, ...data.events };
                        
                        // Update the calendar with changes
                        updateCalendarEvents(data.changes || []);
                    }
                    
                    // Update last fetch time
                    CalendarApp.lastFetchTime = data.timestamp || new Date().toISOString();
                    
                } catch (e) {
                    console.error('Error parsing calendar events:', e);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching calendar events:', error);
                
                // Fall back to full refresh on error
                $("#CalendarsToRefresh").load(`${currentPageUrl} #CalendarsToRefresh`, function() {
                    // Reinitialize draggable items
                    setupDragDropHandlers();
                    
                    // Reapply filter states
                    applyFilterStates(filterStates);
                });
            },
            complete: function() {
                // Remove the refresh indicator
                if (refreshIndicator && refreshIndicator.parentNode) {
                    refreshIndicator.parentNode.removeChild(refreshIndicator);
                }
                
                CalendarApp.isLoading = false;
            }
        });
    }
    
    // Update specific event cells based on changes
    function updateCalendarEvents(changes) {
        if (!changes || changes.length === 0) return;
        
        changes.forEach(change => {
            if (change.type === 'add' || change.type === 'update') {
                // Find the cell for this date
                const cell = document.querySelector(`li[data-id="${change.date}"]`);
                if (!cell) return;
                
                if (change.type === 'update') {
                    // Remove existing event if it's an update
                    const existingEvent = cell.querySelector(`.event[EventID="${change.eventId}"]`);
                    if (existingEvent) {
                        cell.removeChild(existingEvent);
                    }
                }
                
                // Create and append the new event element
                const eventElement = createEventElement(change.event);
                if (eventElement) {
                    cell.appendChild(eventElement);
                    
                    // Apply filter visibility
                    const eventType = change.event.eventType;
                    if (eventType && !CalendarApp.eventFilters[eventType]) {
                        eventElement.style.display = 'none';
                    }
                }
            } else if (change.type === 'remove') {
                // Find and remove the event
                const existingEvent = document.querySelector(`.event[EventID="${change.eventId}"]`);
                if (existingEvent && existingEvent.parentNode) {
                    existingEvent.parentNode.removeChild(existingEvent);
                }
            }
        });
        
        // Re-initialize draggable events
        setupDragDropHandlers();
    }
    
    // Create an event DOM element
    function createEventElement(event) {
        if (!event) return null;
        
        const div = document.createElement('div');
        div.innerHTML = event.html;
        
        // Return the first child, which should be the complete event element
        return div.firstChild;
    }
    
    // Apply filter states to events
    function applyFilterStates(filterStates) {
        document.querySelectorAll(".event").forEach(event => {
            const eventType = event.getAttribute("EventType");
            if (eventType && filterStates.hasOwnProperty(eventType)) {
                event.style.display = filterStates[eventType] ? "block" : "none";
            }
        });
    }

        window.updateEventVisibility = updateEventVisibility;

    
    // Setup optimized periodic updates with progressive intervals
// Dans calendar-optimized.js

// Fonction simplifiée pour les mises à jour périodiques
function setupPeriodicUpdates() {
    const updateInterval = 30000; // 30 secondes
    
    function refreshCalendar() {
        console.log('Refreshing calendar data');
        
        // Sauvegarder l'état des filtres
        const filterStates = {
            PhoneNote: document.getElementById("filter-phone-note")?.checked !== false,
            CalendarEvent: document.getElementById("filter-calendar-event")?.checked !== false,
            ShipmentArrived: document.getElementById("filter-shipment-arrived")?.checked !== false,
            ShipmentETA: document.getElementById("filter-shipment-eta")?.checked !== false
        };
        
        // Utiliser l'approche la plus simple mais fonctionnelle
        fetch('ajax.php?getCalendarEvents=simple')
            .then(response => response.text())
            .then(data => {
                if (data) {
                    try {
                        const jsonData = JSON.parse(data);
                        updateCalendarWithNewData(jsonData);
                    } catch (e) {
                        console.error('Error parsing calendar data:', e);
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching calendar updates:', error);
            })
            .finally(() => {
                // Réappliquer les filtres
                updateEventVisibility();
                
                // Planifier la prochaine mise à jour
                setTimeout(refreshCalendar, updateInterval);
            });
    }
    
    // Commencer les mises à jour après un délai initial
    setTimeout(refreshCalendar, updateInterval);
}

// Fonction pour mettre à jour le calendrier avec de nouvelles données
function updateCalendarWithNewData(data) {
    if (!data || !data.events) return;
    
    // Pour chaque date avec des événements
    Object.keys(data.events).forEach(date => {
        const cell = document.querySelector(`li[data-id="${date}"]`);
        if (cell) {
            // Nettoyer les événements existants ou les mettre à jour selon la stratégie
            const existingEvents = cell.querySelectorAll('.event');
            existingEvents.forEach(event => {
                cell.removeChild(event);
            });
            
            // Ajouter les nouveaux événements
            const fragment = document.createRange().createContextualFragment(data.events[date]);
            cell.appendChild(fragment);
        }
    });
}
    
    // Add CSS for new visual elements
    function addOptimizedStyles() {
        const styleEl = document.createElement('style');
        styleEl.textContent = `
            /* Loading indicators */
            .calendar-refreshing {
                position: fixed;
                bottom: 10px;
                right: 10px;
                background: rgba(255,255,255,0.8);
                padding: 5px 10px;
                border-radius: 3px;
                display: flex;
                align-items: center;
                font-size: 12px;
                z-index: 9999;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            
            .refresh-spinner, .cell-loading .spinner {
                width: 16px;
                height: 16px;
                border: 2px solid #4a89dc;
                border-radius: 50%;
                border-top-color: transparent;
                animation: spin 1s linear infinite;
                margin-right: 5px;
            }
            
            .cell-loading {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(255,255,255,0.7);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 10;
            }
            
            /* Droppable highlight */
            .drop-hover {
                background-color: #e6f7ff !important;
                box-shadow: inset 0 0 0 2px #4a89dc !important;
            }
            
            /* Context menu improvements */
            .show {
                animation: fadeIn 0.1s ease-out;
            }
            
            /* Animation keyframes */
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(5px); }
                to { opacity: 1; transform: translateY(0); }
            }
        `;
        document.head.appendChild(styleEl);
    }
    
    // Initialize the application
    addOptimizedStyles();
    initCalendar();
});


// Fonction de diagnostic
function debugCalendar() {
    console.log('Running calendar diagnostics');
    
    // Compter les événements par type
    const allEvents = document.querySelectorAll('.event');
    const calendarEvents = document.querySelectorAll('.event[EventType="CalendarEvent"]');
    const phoneNotes = document.querySelectorAll('.event[EventType="PhoneNote"]');
    const shipmentArrivedEvents = document.querySelectorAll('.event[EventType="ShipmentArrived"]');
    const shipmentETAEvents = document.querySelectorAll('.event[EventType="ShipmentETA"]');
    
    console.log(`Total events: ${allEvents.length}`);
    console.log(`Calendar events: ${calendarEvents.length}`);
    console.log(`Phone Notes: ${phoneNotes.length}`);
    console.log(`Shipment Arrived: ${shipmentArrivedEvents.length}`);
    console.log(`Shipment ETA: ${shipmentETAEvents.length}`);
    
    // Vérifier l'état des filtres
    console.log('Filter states:', CalendarApp.eventFilters);
}

// Appeler la fonction au chargement
setTimeout(debugCalendar, 1000);