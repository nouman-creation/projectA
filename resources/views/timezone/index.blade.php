<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>World Time Zone Converter</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">World Time Zone Converter</h1>
            
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="mb-6">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Cities</label>
                    <div class="flex gap-2">
                        <input type="text" id="search" class="flex-1 p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="Enter city name (e.g., London, Tokyo, New York)">
                        <button id="searchBtn" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Search</button>
                    </div>
                </div>

                <div id="searchResults" class="space-y-2 max-h-60 overflow-y-auto hidden">
                    <!-- Search results will be displayed here -->
                </div>
            </div>

            <div id="selectedTimezones" class="space-y-4">
                <!-- Selected timezones will be displayed here -->
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let selectedTimezones = new Set();

            function updateTimes() {
                if (selectedTimezones.size === 0) {
                    $('#selectedTimezones').html('<div class="text-center text-gray-500">No cities selected. Search and click on a city to add it.</div>');
                    return;
                }

                $.ajax({
                    url: '{{ route("timezone.getTime") }}',
                    method: 'POST',
                    data: {
                        timezones: Array.from(selectedTimezones),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#selectedTimezones').empty();
                        response.times.forEach(function(timeInfo) {
                            $('#selectedTimezones').append(`
                                <div class="bg-white rounded-lg shadow-lg p-6">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h3 class="text-xl font-semibold text-gray-800">${timeInfo.city}</h3>
                                            <p class="text-gray-600">${timeInfo.country}</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-3xl font-bold text-gray-800">${timeInfo.time}</div>
                                            <div class="text-gray-600">${timeInfo.date}</div>
                                            <div class="text-gray-600">UTC${timeInfo.offset}</div>
                                        </div>
                                        <button class="remove-timezone px-3 py-1 text-red-500 hover:text-red-700" data-timezone="${timeInfo.timezone}">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            `);
                        });
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON?.error || 'An error occurred';
                        $('#selectedTimezones').html(`<div class="text-red-500 p-4 bg-red-50 rounded">${error}</div>`);
                    }
                });
            }

            function performSearch() {
                const search = $('#search').val().trim();
                if (!search) {
                    $('#searchResults').addClass('hidden');
                    return;
                }

                $.ajax({
                    url: '{{ route("timezone.searchCities") }}',
                    method: 'GET',
                    data: { search: search },
                    success: function(response) {
                        $('#searchResults').empty().removeClass('hidden');
                        if (response.results.length === 0) {
                            $('#searchResults').append(`
                                <div class="p-2 text-gray-500">No cities found matching "${search}"</div>
                            `);
                        } else {
                            response.results.forEach(function(result) {
                                $('#searchResults').append(`
                                    <div class="p-2 hover:bg-gray-100 rounded cursor-pointer city-result" 
                                         data-timezone="${result.timezone}"
                                         data-city="${result.city}"
                                         data-country="${result.country}">
                                        <div class="font-medium">${result.city}</div>
                                        <div class="text-sm text-gray-600">${result.country}</div>
                                    </div>
                                `);
                            });
                        }
                    },
                    error: function() {
                        $('#searchResults').html(`
                            <div class="p-2 text-red-500">Error searching for cities</div>
                        `).removeClass('hidden');
                    }
                });
            }

            $('#searchBtn').click(performSearch);
            
            $('#search').on('keypress', function(e) {
                if (e.which === 13) {
                    performSearch();
                }
            });

            $(document).on('click', '.city-result', function() {
                const timezone = $(this).data('timezone');
                if (!selectedTimezones.has(timezone)) {
                    selectedTimezones.add(timezone);
                    updateTimes();
                }
                $('#search').val('');
                $('#searchResults').addClass('hidden');
            });

            $(document).on('click', '.remove-timezone', function() {
                const timezone = $(this).data('timezone');
                selectedTimezones.delete(timezone);
                updateTimes();
            });

            // Initial update
            updateTimes();
            
            // Update times every second
            setInterval(updateTimes, 1000);
        });
    </script>
</body>
</html> 