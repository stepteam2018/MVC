<?php

namespace app\configuration;


class PassHashConfiguration{

    const ALGORITHM = "sha256";
    const SALT_POS = 3;
    const SALT_LEN = 5;

}