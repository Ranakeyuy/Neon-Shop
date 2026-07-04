#include <Arduino.h>
#include "HX711.h"

const int LOADCELL_DOUT_PIN = 4;
const int LOADCELL_SCK_PIN = 5;
const float calibration_factor = 2280.f;

HX711 scale;

int age = 0;
int current_page = 0;
float earth_weight = 0.0;
unsigned long last_poll_time = 0;
unsigned long last_scale_time = 0;

const float gravity_factors[] = {
    0.000f,
    0.378f,
    0.907f,
    1.000f,
    0.377f,
    2.528f,
    1.064f,
    0.889f,
    1.125f,
    0.067f,
    0.166f,
    0.000f
};

void pollPageId() {
    uint8_t pollCmd[] = {0x5A, 0xA5, 0x04, 0x83, 0x00, 0x14, 0x01};
    Serial2.write(pollCmd, sizeof(pollCmd));
}

void updateWeightDisplay(int weightCentigrams) {
    uint8_t writeCmd[] = {
        0x5A, 0xA5, 0x05, 0x82, 0x30, 0x00,
        (uint8_t)((weightCentigrams >> 8) & 0xFF),
        (uint8_t)(weightCentigrams & 0xFF)
    };
    Serial2.write(writeCmd, sizeof(writeCmd));
}

void updateAgeDisplay(int ageVal) {
    uint8_t writeCmd[] = {
        0x5A, 0xA5, 0x05, 0x82, 0x20, 0x00,
        (uint8_t)((ageVal >> 8) & 0xFF),
        (uint8_t)(ageVal & 0xFF)
    };
    Serial2.write(writeCmd, sizeof(writeCmd));
}

void readDwinPackets() {
    while (Serial2.available() > 0) {
        if (Serial2.read() == 0x5A) {
            if (Serial2.read() == 0xA5) {
                while (Serial2.available() < 1) {}
                uint8_t len = Serial2.read();
                uint8_t buffer[64];
                int idx = 0;
                while (idx < len) {
                    if (Serial2.available() > 0) {
                        buffer[idx++] = Serial2.read();
                    }
                }
                uint8_t cmd = buffer[0];
                uint16_t vp = (buffer[1] << 8) | buffer[2];
                if (cmd == 0x83) {
                    if (vp == 0x0014) {
                        current_page = (buffer[4] << 8) | buffer[5];
                    } else if (vp == 0x2000) {
                        age = (buffer[4] << 8) | buffer[5];
                    }
                }
            }
        }
    }
}

void setup() {
    Serial.begin(115200);
    Serial2.begin(115200, SERIAL_8N1, 16, 17);
    scale.begin(LOADCELL_DOUT_PIN, LOADCELL_SCK_PIN);
    scale.set_scale(calibration_factor);
    scale.tare();
}

void loop() {
    readDwinPackets();
    unsigned long current_time = millis();
    if (current_time - last_poll_time >= 200) {
        pollPageId();
        last_poll_time = current_time;
    }
    if (current_time - last_scale_time >= 500) {
        if (scale.is_ready()) {
            earth_weight = scale.get_units(3);
            if (earth_weight < 0.0f) {
                earth_weight = 0.0f;
            }
        }
        if (current_page >= 1 && current_page <= 10) {
            float planet_weight = earth_weight * gravity_factors[current_page];
            int weightCentigrams = (int)(planet_weight * 100.0f);
            updateWeightDisplay(weightCentigrams);
            updateAgeDisplay(age);
        }
        last_scale_time = current_time;
    }
}
