/**
 * ==================================================
 * Developer: Alexey Nazarov
 * E-mail: jc1988x@gmail.com
 * Copyright (c) 2019 - 2022
 * ==================================================
 * "Bit.Umc - Bitrix integration" - bundle.config.js
 * 10.07.2022 22:37
 * ==================================================
 */
module.exports = {
    input: 'src/admin.js',
    output: 'dist/admin.bundle.js',
    namespace: 'BX.Anz.Appointment',
    browserslist: false,
    minification: true,
    plugins: {
        resolve: true,
    },
};