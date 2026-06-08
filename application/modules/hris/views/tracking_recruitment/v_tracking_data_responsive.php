<style>
    .tracking-container {
        padding: 20px 10px;
        overflow-x: auto;
    }

    .tracking-main {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        position: relative;
        gap: 60px;
        min-height: 120px;
        flex-wrap: nowrap;
    }

    .tracking-line {
        position: absolute;
        top: 50%;
        left: 5%;
        right: 5%;
        height: 2px;
        background-color: #cbc8c8;
        z-index: 1;
        transform: translateY(-50%);
    }

    .tracking-item {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 10px;
        z-index: 2;
        flex: 0 0 auto;
    }

    .tracking-circle {
        border: 5px solid #E8E8E8;
        border-radius: 50%;
        width: 70px;
        height: 70px;
        background-color: #97F0A6;
        flex-shrink: 0;
    }

    .tracking-item label {
        text-align: center;
        font-size: 14px;
        max-width: 100px;
    }

    /* Desktop - 992px and above */
    @media (min-width: 992px) {
        .tracking-main {
            gap: 80px;
        }
    }

    /* Tablet - 768px to 991px */
    @media (max-width: 991px) and (min-width: 768px) {
        .tracking-main {
            gap: 50px;
        }

        .tracking-circle {
            width: 65px;
            height: 65px;
        }

        .tracking-item label {
            font-size: 13px;
            max-width: 90px;
        }
    }

    /* Mobile - 481px to 767px */
    @media (max-width: 767px) and (min-width: 481px) {
        .tracking-main {
            gap: 40px;
            min-height: 100px;
        }

        .tracking-circle {
            width: 60px;
            height: 60px;
        }

        .tracking-item label {
            font-size: 12px;
            max-width: 80px;
        }
    }

    /* Mobile - up to 480px */
    @media (max-width: 480px) {
        .tracking-container {
            padding: 20px 5px;
        }

        .tracking-main {
            flex-direction: column;
            gap: 20px;
            min-height: auto;
        }

        .tracking-line {
            display: none;
        }

        .tracking-circle {
            width: 50px;
            height: 50px;
        }

        .tracking-item label {
            font-size: 11px;
            max-width: 70px;
        }
    }

    /* Very small mobile - 360px and below */
    @media (max-width: 360px) {
        .tracking-container {
            padding: 15px 5px;
        }

        .tracking-circle {
            width: 45px;
            height: 45px;
            border-width: 3px;
        }

        .tracking-item label {
            font-size: 10px;
            max-width: 60px;
        }
    }
</style>

<div class="tracking-container">
    <div class="tracking-main">
        <div class="tracking-line"></div>
        
        <div class="tracking-item">
            <span class="tracking-circle"></span>
            <label for="">Usulan Karyawan Baru</label>
        </div>

        <div class="tracking-item">
            <span class="tracking-circle"></span>
            <label for="">Data Kandidat Masuk</label>
        </div>

        <div class="tracking-item">
            <span class="tracking-circle"></span>
            <label for="">Probation Karyawan</label>
        </div>
    </div>
</div>