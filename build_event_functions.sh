#!/usr/bin/env bash
set -eo pipefail
[[ "${DEBUG}" == "true" ]] && set -x

# Define the CSV file containing event names
CSV_FILE="eventList.csv"
OUTPUT_FILE="generated_code.php"
JSON_FILE="generated_events.json"
JSON_ONLINE_FILE="generated_oneline_events.json"

# Start writing the PHP file
echo "<?php" > "$OUTPUT_FILE"
echo "class EventHandler {" >> "$OUTPUT_FILE"
echo "    public function init() {" >> "$OUTPUT_FILE"

# Read each event from CSV and generate the subscribe lines
while IFS=',' read -r event type; do
    echo "        \$this->subscribe('$event');" >> "$OUTPUT_FILE"
done < "$CSV_FILE"

echo "    }" >> "$OUTPUT_FILE"

echo "" >> "$OUTPUT_FILE"

# Read each event from CSV and generate the event handling functions
while IFS=',' read -r event type; do
    echo "    public function $event() {" >> "$OUTPUT_FILE"
    echo "        if (\$this->isEventOn('$event')) {" >> "$OUTPUT_FILE"
    if [[ $type == "surveyStatus" ]]; then
        echo "            \$this->callWebhookSurvey('$event');" >> "$OUTPUT_FILE"
    else 
        echo "            \$this->callWebhook('$event');" >> "$OUTPUT_FILE"
    fi
    echo "        }" >> "$OUTPUT_FILE"
    echo "        return;" >> "$OUTPUT_FILE"
    echo "    }" >> "$OUTPUT_FILE"
    echo "" >> "$OUTPUT_FILE"
done < "$CSV_FILE"

# Initialize an empty JSON object
declare -A event_map
# Read each event from CSV and populate the JSON object
while IFS=',' read -r event type; do
    # Skip empty lines or headers
    if [[ -z "$event" || "$event" == "event" ]]; then
        continue
    fi

    # If type does not exist, initialize it as an empty JSON object
    if [[ -z "${event_map[$type]}" ]]; then
        event_map[$type]=""
    fi

    # Append the event to the type's JSON object
    event_map[$type]+="$event "
done < "$CSV_FILE"

# Convert the associative array into a valid JSON structure
json_output="{"
first_type=true
for type in "${!event_map[@]}"; do
    if [ "$first_type" = false ]; then
        json_output+=","
    fi
    first_type=false
    json_output+="\"$type\": {"
    first_event=true
    for event in ${event_map[$type]}; do
        if [ "$first_event" = false ]; then
            json_output+=","
        fi
        first_event=false
        json_output+="\"$event\": false" >> "$OUTPUT_FILE"
    done
    json_output+="}"
done
json_output+="}"
echo $json_output

# Save the JSON object to a file
echo "$json_output" | jq -c '.' > "$JSON_ONLINE_FILE"
echo "$json_output" | jq '.' > "$JSON_FILE"

echo "JSON file generated: $JSON_FILE"

echo "    /**" >> "$OUTPUT_FILE"
echo "     * Return global default permission" >> "$OUTPUT_FILE"
echo "     * @return string" >> "$OUTPUT_FILE"
echo "     */" >> "$OUTPUT_FILE"
echo "    private static function getDefaultEvents() {" >> "$OUTPUT_FILE"
echo "        return json_encode([" >> "$OUTPUT_FILE"

for type in "${!event_map[@]}"; do
    echo "            '$type' => [" >> "$OUTPUT_FILE"
    for event in ${event_map[$type]}; do
        echo "              '$event' => false," >> "$OUTPUT_FILE"
    done
    echo "            ]," >> "$OUTPUT_FILE"
done

echo "        ]);" >> "$OUTPUT_FILE"
echo "    }" >> "$OUTPUT_FILE"
echo "}" >> "$OUTPUT_FILE"
echo "?>" >> "$OUTPUT_FILE"


echo "PHP code generated in $OUTPUT_FILE"